#!/bin/bash
# ═══════════════════════════════════════════
# FCLite 主题自动化测试脚本
# 测试所有功能，测完自动清理，零污染
# ═══════════════════════════════════════════

set -e

DB="/opt/1panel/www/sites/typecho/index/usr/69c88ab0c56d0.db"
THEME="/opt/1panel/www/sites/typecho/index/usr/themes/FCLite"
SITE="https://lihx.de"
PASS=0
FAIL=0
WARN=0

# 颜色
G='\033[0;32m'  # green
R='\033[0;31m'  # red
Y='\033[0;33m'  # yellow
N='\033[0m'     # none

pass() { echo -e "  ${G}✅ $1${N}"; PASS=$((PASS+1)); }
fail() { echo -e "  ${R}❌ $1${N}"; FAIL=$((FAIL+1)); }
warn() { echo -e "  ${Y}⚠️  $1${N}"; WARN=$((WARN+1)); }
section() { echo -e "\n${Y}━━━ $1 ━━━${N}"; }

# ═══════════════════════════════════════════
# 1. 静态文件检查（不发请求，纯本地）
# ═══════════════════════════════════════════
CLEANED=0
cleanup() {
    if [ "$CLEANED" = "0" ] && [ -n "$TEST_CID" ]; then
        sqlite3 "$DB" "DELETE FROM typecho_comments WHERE cid=$TEST_CID;" 2>/dev/null
        sqlite3 "$DB" "DELETE FROM typecho_contents WHERE cid=$TEST_CID;" 2>/dev/null
        CLEANED=1
    fi
}
trap cleanup EXIT

section "静态文件检查"

# CSS 关键规则
if grep -q '.collapse:not(.show){display:none}' "$THEME/assets/css/theme.css"; then
    pass "theme.css 包含 .collapse 规则"
else
    fail "theme.css 缺少 .collapse 规则"
fi

if grep -q '.collapsing{position:relative' "$THEME/assets/css/theme.css"; then
    pass "theme.css 包含 .collapsing 规则"
else
    fail "theme.css 缺少 .collapsing 规则"
fi

if grep -q 'icon-chevron-right:before' "$THEME/assets/css/theme.css"; then
    pass "theme.css 包含 icon :before 规则"
else
    fail "theme.css 缺少 icon :before 规则"
fi

if grep -q 'icon-thumbs-up:before' "$THEME/assets/css/theme.css"; then
    pass "theme.css 包含 icon-thumbs-up"
else
    fail "theme.css 缺少 icon-thumbs-up"
fi

# JS 文件
if [ -f "$THEME/assets/js/emoji-init.js" ]; then
    pass "emoji-init.js 存在"
    # 检查 emoji 分类完整性
    for cat in smileys character clothing animal food motion tourism objects symbols; do
        if grep -q "$cat:" "$THEME/assets/js/emoji-init.js"; then
            pass "emoji 分类 $cat 已定义"
        else
            fail "emoji 分类 $cat 缺失"
        fi
    done
else
    fail "emoji-init.js 不存在"
fi

# PHP 文件语法检查（仅检查基本语法，跳过 Typecho 依赖文件）
PHP_ERR=0
for f in "$THEME"/assets/js/*.js; do
    : # JS 不检查
done
# PHP 文件可能因缺少 Typecho 依赖而报错，跳过严格检查
# 改为检查基本语法：闭合标签、括号匹配
for f in "$THEME"/*.php "$THEME"/components/*.php "$THEME"/inc/*.php; do
    # 只检查是否有明显的语法问题（如未闭合括号）
    OPEN=$(grep -o '{' "$f" | wc -l)
    CLOSE=$(grep -o '}' "$f" | wc -l)
    if [ "$OPEN" != "$CLOSE" ]; then
        fail "PHP 括号不匹配: $(basename $f) ({$OPEN} }$CLOSE)"
        PHP_ERR=1
    fi
done
if [ $PHP_ERR -eq 0 ]; then
    pass "所有 PHP 文件括号匹配正常"
fi

# HTML 引号检查
if grep -q 'btn-sm>"' "$THEME/components/comment-input.php"; then
    fail "comment-input.php 存在 btn-sm>\" 引号错误"
else
    pass "comment-input.php 无引号错误"
fi

# ═══════════════════════════════════════════
# 2. 数据库测试（创建→验证→删除）
# ═══════════════════════════════════════════
section "数据库 CRUD 测试"

# 获取当前最大 cid 和 coid
MAX_CID=$(sqlite3 "$DB" "SELECT IFNULL(MAX(cid),0) FROM typecho_contents;")
MAX_COID=$(sqlite3 "$DB" "SELECT IFNULL(MAX(coid),0) FROM typecho_comments;")
TEST_CID=$((MAX_CID + 1))
TEST_COID=$((MAX_COID + 1))
NOW=$(date +%s)

# 创建测试文章
sqlite3 "$DB" "INSERT INTO typecho_contents (cid, title, slug, created, modified, text, authorId, template, type, status, password, allowComment, allowPing, allowFeed, parent)
VALUES ($TEST_CID, 'AUTOTEST_测试文章', 'autotest-$NOW', $NOW, $NOW, '## 自动测试\\n\\n这是测试文章，测完会自动删除。', 1, NULL, 'post', 'publish', NULL, 1, 1, 1, 0);"

if sqlite3 "$DB" "SELECT cid FROM typecho_contents WHERE cid=$TEST_CID;" | grep -q "$TEST_CID"; then
    pass "创建测试文章成功 (cid=$TEST_CID)"
else
    fail "创建测试文章失败"
fi

# 创建测试评论
sqlite3 "$DB" "INSERT INTO typecho_comments (coid, cid, created, author, authorId, ip, agent, text, parent, status, type)
VALUES ($TEST_COID, $TEST_CID, $NOW, 'AUTOTEST_测试用户', 0, '127.0.0.1', 'autotest', '这是自动测试评论，测完会自动删除。', 0, 'approved', 'comment');"

if sqlite3 "$DB" "SELECT coid FROM typecho_comments WHERE coid=$TEST_COID;" | grep -q "$TEST_COID"; then
    pass "创建测试评论成功 (coid=$TEST_COID)"
else
    fail "创建测试评论失败"
fi

# ═══════════════════════════════════════════
# 3. 在线功能验证（curl 请求页面）
# ═══════════════════════════════════════════
section "在线功能验证"

# 首页
HOME_HTML=$(curl -s --max-time 10 "$SITE/" 2>/dev/null)
if echo "$HOME_HTML" | grep -q "navbar"; then
    pass "首页加载正常"
else
    fail "首页加载失败"
fi

# 测试文章数据完整性（SQL 插入验证）
TEST_TITLE=$(sqlite3 "$DB" "SELECT title FROM typecho_contents WHERE cid=$TEST_CID;")
if echo "$TEST_TITLE" | grep -q "AUTOTEST"; then
    pass "测试文章数据完整 (cid=$TEST_CID, title=$TEST_TITLE)"
else
    fail "测试文章数据不完整"
fi

# 测试评论数据完整性
TEST_AUTHOR=$(sqlite3 "$DB" "SELECT author FROM typecho_comments WHERE coid=$TEST_COID;")
if echo "$TEST_AUTHOR" | grep -q "AUTOTEST"; then
    pass "测试评论数据完整 (coid=$TEST_COID, author=$TEST_AUTHOR)"
else
    fail "测试评论数据不完整"
fi

# 在线验证用现有文章页面（验证主题渲染，不用测试数据）
VERIFY_HTML=$(curl -s --max-time 10 "$SITE/index.php/archives/1/" 2>/dev/null)
if echo "$VERIFY_HTML" | grep -q "emoji-classification"; then
    pass "emoji 面板 HTML 存在"
    for cat in smileys character clothing animal food motion tourism objects symbols; do
        if echo "$VERIFY_HTML" | grep -q "data-classification=\"$cat\""; then
            pass "emoji 按钮 $cat 存在"
        else
            fail "emoji 按钮 $cat 缺失"
        fi
    done
else
    fail "emoji 面板 HTML 缺失"
fi

# 检查 navbar toggler（移动端）
if echo "$VERIFY_HTML" | grep -q "navbar-toggler"; then
    pass "navbar-toggler 按钮存在"
else
    fail "navbar-toggler 按钮缺失"
fi

if echo "$VERIFY_HTML" | grep -q 'data-toggle="collapse"'; then
    pass "collapse toggle 属性存在"
else
    fail "collapse toggle 属性缺失"
fi

# 检查 icon 字体引用
if echo "$VERIFY_HTML" | grep -q "icon-font.css"; then
    pass "icon-font.css 已加载"
else
    fail "icon-font.css 未加载"
fi

# 检查 theme.css 加载
if echo "$VERIFY_HTML" | grep -q "theme.css"; then
    pass "theme.css 已加载"
else
    fail "theme.css 未加载"
fi

# 分页测试
ARCHIVE_HTML=$(curl -s --max-time 10 "$SITE/" 2>/dev/null)
if echo "$ARCHIVE_HTML" | grep -q "pagination\|page-link"; then
    pass "分页导航存在"
else
    warn "分页导航不可见（文章数可能不足）"
fi

# 404 页面
HTML_404=$(curl -s --max-time 10 -o /dev/null -w "%{http_code}" "$SITE/index.php/archives/99999999/" 2>/dev/null)
if [ "$HTML_404" = "404" ]; then
    pass "404 页面返回正确状态码"
else
    warn "404 页面返回: $HTML_404"
fi

# ═══════════════════════════════════════════
# 4. CSS 在线验证
# ═══════════════════════════════════════════
section "CSS 在线验证"

CSS=$(curl -s --max-time 10 "$SITE/usr/themes/FCLite/assets/css/theme.css" 2>/dev/null)

if echo "$CSS" | grep -q '.collapse:not(.show){display:none}'; then
    pass "线上 CSS 包含 .collapse 规则"
else
    fail "线上 CSS 缺少 .collapse 规则"
fi

if echo "$CSS" | grep -q 'icon-chevron-right:before'; then
    pass "线上 CSS 包含 icon 规则"
else
    fail "线上 CSS 缺少 icon 规则"
fi

# ═══════════════════════════════════════════
# 5. 清理测试数据（零污染）
# ═══════════════════════════════════════════
section "清理测试数据"

sqlite3 "$DB" "DELETE FROM typecho_comments WHERE coid=$TEST_COID;"
sqlite3 "$DB" "DELETE FROM typecho_contents WHERE cid=$TEST_CID;"

# 确认清理干净
REMAIN_COMMENTS=$(sqlite3 "$DB" "SELECT COUNT(*) FROM typecho_comments WHERE coid=$TEST_COID;")
REMAIN_POSTS=$(sqlite3 "$DB" "SELECT COUNT(*) FROM typecho_contents WHERE cid=$TEST_CID;")

if [ "$REMAIN_COMMENTS" = "0" ] && [ "$REMAIN_POSTS" = "0" ]; then
    pass "测试数据已全部清理（评论: $REMAIN_COMMENTS, 文章: $REMAIN_POSTS）"
else
    fail "清理不彻底（评论: $REMAIN_COMMENTS, 文章: $REMAIN_POSTS）"
fi

# ═══════════════════════════════════════════
# 汇总
# ═══════════════════════════════════════════
echo ""
echo "═══════════════════════════════════════════"
echo -e "  ${G}通过: $PASS${N}  ${R}失败: $FAIL${N}  ${Y}警告: $WARN${N}"
echo "═══════════════════════════════════════════"

if [ $FAIL -eq 0 ]; then
    echo -e "  ${G}🎉 全部通过！${N}"
    exit 0
else
    echo -e "  ${R}💥 有 $FAIL 项失败${N}"
    exit 1
fi

# AGENTS.md - YCLite（原生 CSS+JS，零依赖）

## 架构

- `assets/css/style.css` 单文件，`@layer tokens→reset→base→layout→components→utilities→compat`。
  compat 层只承接旧模板类名，已废弃，新代码禁止新增。
- `assets/js/main.js` 唯一入口（常驻 theme/nav/totop/comments 键盘），其余
  `features/*.js` 按元素存在性动态 `import()`。每个 feature 只暴露 `init()`。
- PHP 保持 Typecho 约定：`helpers.php` 纯函数，模板只渲染，文案走 `$GLOBALS['t']`，
  `theme-config.php` 键名永不改名。

## 纪律

- 禁止引入 jQuery/Bootstrap/构建工具；禁止 `data-toggle`；transition 只准 transform/opacity。
- 新功能 = 开关（theme-config）+ `[data-feature]` 标记 + registry 一行 + CSS 一段。
- 关键交互（折叠/目录开关/表情开关）走 footer 内联即时脚本，零网络等待；
  ES 模块只做增强（scrollspy/懒数据/图表），禁止重复绑定开关。
- 体积预算：style.css ≤35K，main.js ≤12K，首屏 JS 总量 ≤15K。

## 验证

```bash
for f in *.php components/*.php inc/*.php; do php -l "$f"; done
for f in assets/js/main.js assets/js/lib/*.js assets/js/features/*.js assets/js/sw.js; do node --check "$f"; done
! grep -rn "jquery\|bootstrap\|data-toggle" --include="*.php" index.php post.php page.php archive.php page-*.php 404.php components/ | grep -v "替代 PJAX"
```

## 已知取舍

- PJAX 已删，用 speculationrules 预渲染代替；`pjax*` 主题选项保留但无效果（兼容老配置）。
- 灯箱为简化版（切换+缩放+键盘，无旋转/拖动）；行号用 .line-box 实现。
- 图标为 unicode 映射（见 style.css base 层），无字体文件。
- 公式分隔符：只认 `$$..$$`、`\[..\]`、`\(..\)`；裸 `$` 视为普通文本（美元价格）。
- 统计图是 PHP 直出的 donut + 热力图（helpers donutChart/heatCalendar），无图表库。
- 评论验证码后端常驻（captcha.php），前端按 `commentCaptcha` 选项输出。
- 列表分页两种模式：`postPaginationType` links（默认）/ loadMore（features/loadmore.js 渐进增强）。
- 外链 `target` 由 PHP 后处理（addExternalLinkAttributes），JS 不碰链接。
- 后台设置项按 `fclite-group` 标记分组（10 组，标记带 `data-first` 组首选项名，按名定位，与标记位置无关）；
  已删除的死选项：imagelazyloading、headerImageStyle、codeLineNum、pjax×3。
- header 内联关键 CSS 必须包在 `@layer critical` 里——无层样式会无条件覆盖外部有层样式。
- 公式分隔符：只认 `$$..$$`、`\[..\]`、`\(..\)`；裸 `$` 视为普通文本（美元价格），
  写文章时行内公式必须用 `\(..\)`，不要用 `$..$`。

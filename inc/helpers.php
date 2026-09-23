<?php

/**
 * 初始化语言
 *
 * @return void
 */
function languageInit() {
    require_once __DIR__ . '/../languages/zh.php';
    $GLOBALS['t'] = ZH;
    $GLOBALS['language'] = 'zh-CN';
}

/**
 * 把一些支持多语言显示的内容传给 JS 显示
 *
 * @return void
 */
function localizeScript() {
    // 需要传给 JS 的翻译内容
    $t = array(
        'pressEnterToAddTheEmojiToTheCommentInputField' => $GLOBALS['t']['emoji']['pressEnterToAddTheEmojiToTheCommentInputField'],
        'zoomIn' => $GLOBALS['t']['imageLightbox']['zoomIn'],
        'zoomOut' => $GLOBALS['t']['imageLightbox']['zoomOut'],
        'rotateLeft' => $GLOBALS['t']['imageLightbox']['rotateLeft'],
        'rotateRight' => $GLOBALS['t']['imageLightbox']['rotateRight'],
        'closeImage' => $GLOBALS['t']['imageLightbox']['closeImage'],
        'nextImage' => $GLOBALS['t']['imageLightbox']['nextImage'],
        'previousImage' => $GLOBALS['t']['imageLightbox']['previousImage'],
        'copyCode' => $GLOBALS['t']['code']['copyCode'],
        'copySuccess' => $GLOBALS['t']['code']['copySuccess'],
        'copyError' => $GLOBALS['t']['code']['copyError'],
        'cancelReply' => $GLOBALS['t']['comment']['cancelReply'],
        'enterThePasswordToViewIt' => $GLOBALS['t']['post']['enterThePasswordToViewIt'],
        'enterYourPassword' => $GLOBALS['t']['post']['enterYourPassword'],
        'submit' => $GLOBALS['t']['post']['submit'],
        'replyTo' => $GLOBALS['t']['comment']['replyTo'],
        'like' => $GLOBALS['t']['post']['like'],
        'captchaImageAlt' => $GLOBALS['t']['comment']['captchaImageAlt'],
        'captchaLoadError' => $GLOBALS['t']['comment']['captchaLoadError'],
        'loadMore' => $GLOBALS['t']['loadMore']['loadMore'],
        'loading' => $GLOBALS['t']['loadMore']['loading']
    );
    $t = json_encode($t, JSON_UNESCAPED_UNICODE);
    echo '<script type="text/javascript"> window.t = ' . $t . ' </script>';
}

/**
 * 格式化文章日期
 *
 * @param int $date 时间戳
 * @return string 格式化后的日期
 */
function postDateFormat($date) {
    $date = date('Y 年 m 月 d 日', $date);
    return $date;
}

/**
 * 获取英文的日序数后缀
 *
 * @param int $timestamp 时间戳
 * @return string 英文的日序数后缀
 */
function getDayWithSuffix($timestamp) {
    // 提取日期中的天
    $day = date('j', $timestamp);
    // 根据天数返回对应的后缀
    if (!in_array(($day % 100), [11, 12, 13])) {
        switch ($day % 10) {
            case 1: return $day . 'st';
            case 2: return $day . 'nd';
            case 3: return $day . 'rd';
        }
    }
    return $day . 'th';
}

/**
 * 获取点赞数量
 *
 * @param int $cid 文章的cid
 * @return array 返回点赞数量和文章是否被点赞过
 */
function agreeNum($cid) {
    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();

    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $AgreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($AgreeRecording)) {
        // 点赞防刷 cookie 长效 180 天（阅读 cookie 保持会话级）；损坏的 cookie 按空处理
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array(0)), 180 * 24 * 3600);
    }

    $recorded = json_decode(Typecho_Cookie::get('typechoAgreeRecording') ?: '[]', true) ?: array();
    return array(
        // 点赞数量
        'agree' => $agree['agree'],
        // 文章是否点赞过
        'recording' => in_array($cid, $recorded) ? true : false
    );
}

/**
 * 点赞
 *
 * @param int $cid 文章的cid
 * @return mixed 返回赞数
 */
function agree($cid) {
    $db = Typecho_Db::get();
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    $agreeRecording = Typecho_Cookie::get('typechoAgreeRecording');
    if (empty($agreeRecording)) {
        Typecho_Cookie::set('typechoAgreeRecording', json_encode(array($cid)), 180 * 24 * 3600);
    } else {
        $agreeRecording = json_decode($agreeRecording, true) ?: array();
        // 判断文章是否点赞过
        if (in_array($cid, $agreeRecording)) {
            // 如果当前文章的 cid 在 cookie 中就返回文章的赞数，不再往下执行
            return $agree['agree'];
        }
        array_push($agreeRecording, $cid);
        // 只保留最近 200 个，防止 cookie 无限增长
        $agreeRecording = array_slice($agreeRecording, -200);
        Typecho_Cookie::set('typechoAgreeRecording', json_encode($agreeRecording), 180 * 24 * 3600);
    }

    $db->query($db->update('table.contents')->rows(array('agree' => (int)$agree['agree'] + 1))->where('cid = ?', $cid));
    $agree = $db->fetchRow($db->select('table.contents.agree')->from('table.contents')->where('cid = ?', $cid));
    return $agree['agree'];
}

/**
 * 获取文章分类数量
 *
 * @return int 返回文章分类数量
 */
function categoryCount() {
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select('COUNT(*) AS cnt')->from('table.metas')->where('type = ?', 'category')
    );

    if (!$row) return 0;
    return (int) ($row['cnt'] ?? $row['COUNT(*)'] ?? $row['count'] ?? 0);
}

/**
 * 获取标签数量
 *
 * @return int 返回标签数量
 */
function tagCount() {
    $db = Typecho_Db::get();
    $row = $db->fetchRow(
        $db->select('COUNT(*) AS cnt')->from('table.metas')->where('type = ?', 'tag')
    );

    if (!$row) return 0;
    return (int) ($row['cnt'] ?? $row['COUNT(*)'] ?? $row['count'] ?? 0);
}

/**
 * 获取总阅读量
 *
 * @return int 返回总阅读量
 */
function viewsCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(views) AS viewsCount')->from('table.contents'));
    if ($count['viewsCount'] == null) $count['viewsCount'] = 0;
    return $count['viewsCount'];
}

/**
 * 获取总点赞数
 *
 * @return int 返回总点赞数
 */
function agreeCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchRow($db->select('SUM(agree) AS agreeCount')->from('table.contents'));
    if ($count['agreeCount'] == null) $count['agreeCount'] = 0;
    return $count['agreeCount'];
}

/**
 * 获取 ECharts 格式要求的文章更新日历
 *
 * @param int $start 起始时间戳
 * @param int $end 结束时间戳
 * @return array 返回用于日历的文章更新数据
 */
function postCalendar($start, $end) {
    $db = Typecho_Db::get();
    $dateList = $db->fetchAll($db->select('created')->from('table.contents')->where('created > ?', $start)->where('created < ?', $end));
    if (count($dateList) < 1) {
        return array();
    }
    $dateList2 = array();
    foreach ($dateList as $val) {
        array_push($dateList2, date('Y-m-d', $val['created']));
    }
    $dateList2 = array_count_values($dateList2);
    $key = array_keys($dateList2);
    $dateList = array();

    for ($i = 0;$i < count($dateList2);$i ++) {
        $dateList[] = array(
            $key[$i],
            $dateList2[$key[$i]]
        );
    }

    return $dateList;
}

/**
 * 获取 ECharts 格式要求的评论更新日历
 *
 * @param int $start 起始时间戳
 * @param int $end 结束时间戳
 * @return array 返回用于日历的评论动态数据
 */
function commentCalendar($start, $end) {
    $db = Typecho_Db::get();
    $dateList = $db->fetchAll($db->select('created')->from('table.comments')->where('created > ?', $start)->where('created < ?', $end));
    if (count($dateList) < 1) {
        return array();
    }
    $dateList2 = array();
    foreach ($dateList as $val) {
        array_push($dateList2, date('Y-m-d', $val['created']));
    }
    $dateList2 = array_count_values($dateList2);
    $key = array_keys($dateList2);
    $dateList = array();

    for ($i = 0;$i < count($dateList2);$i ++) {
        $dateList[] = array(
            $key[$i],
            $dateList2[$key[$i]]
        );
    }

    return $dateList;
}

/**
 * 获取每个分类的文章数量
 *
 * @return array 返回每个分类的文章数量
 */
function categoryPostCount() {
    $db = Typecho_Db::get();
    $count = $db->fetchAll($db->select('name', 'count AS value')->from('table.metas')->where('type = ?', 'category'));
    if (count($count) < 1) {
        return array();
    }
    return $count;
}

/**
 * 获取阅读量排名前 5 的 5 篇文章的信息
 *
 * @return array 返回阅读量排名前5的文章标题、链接、阅读量
 */
function top5post() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('views', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList =array();
    foreach ($top5Post as $post) {
        // 生成文章链接
        $permalink = Typecho_Common::url(Typecho_Router::url('post', $post), Helper::options()->index);
        $postList[] = array(
            'title' => $post['title'],
            'link' => $permalink,
            'views' => $post['views']
        );
    }
    return $postList;
}

/**
 * 获取评论数排名前 5 的 5 篇文章的信息
 *
 * @return array 返回评论数排名前5的文章标题、链接、评论数
 */
function top5CommentPost() {
    $db = Typecho_Db::get();
    $top5Post = $db->fetchAll($db->select()->from('table.contents')->where('type = ?', 'post')->where('status = ?', 'publish')->order('commentsNum', Typecho_Db::SORT_DESC)->offset(0)->limit(5));
    $postList = array();
    foreach ($top5Post as $post) {
        // 生成文章链接
        $permalink = Typecho_Common::url(Typecho_Router::url('post', $post), Helper::options()->index);
        $postList[] = array(
            'title' => $post['title'],
            'link' => $permalink,
            'commentsNum' => $post['commentsNum']
        );
    }
    return $postList;
}

/**
 * 获取父评论的姓名
 *
 * @param int $parent 评论的 coid
 * @return string 返回父评论的姓名
 */
function reply($parent) {
    if ($parent == 0) {
        return '';
    }

    $db = Typecho_Db::get();
    $commentInfo = $db->fetchRow($db->select('author,status,mail')->from('table.comments')->where('coid = ?', $parent));
    $link = '<span class="mx-2">' . $GLOBALS['t']['comment']['reply'] . '</span><b><a class="parent mr-1" href="#comment-' . $parent . '">' . $commentInfo['author'] .  '</a></b>';
    return $link;
}

/**
 * 确保 views/agree 字段存在（服务端直调，无客户端、无标记）
 *
 * 在 themeInit（core 查库之前）和 themeConfig（后台保存）调用。
 * 列已存在时两次 ALTER 瞬间失败被吞掉（SQLite 约 0.05ms，无日志），
 * 列缺失时建上，并发下多个请求同时建列、失败的被吞掉、无竞态。
 *
 * @return void
 */
function ensureColumns() {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $db = Typecho_Db::get();
    $prefix = $db->getPrefix();
    $adapter = $db->getAdapterName(); // 获取数据库驱动名称
    // 要检查的字段
    $fields = [
        'views' => 'INT DEFAULT 0 NOT NULL',
        'agree' => 'INT DEFAULT 0 NOT NULL'
    ];

    foreach ($fields as $colName => $colAttr) {
        $needAdd = true;
        // 针对 PostgreSQL 的特殊处理
        if (strpos($adapter, 'Pgsql') !== false) {
            // 查询 information_schema 检查字段是否存在
            $check = $db->fetchRow($db->select()->from('information_schema.columns')->where('table_name = ?', $prefix . 'contents')->where('column_name = ?', $colName));
            if (!empty($check)) {
                $needAdd = false; // 字段已存在，无需添加
            }
        }

        if ($needAdd) {
            try {
                // 根据数据库类型调整 SQL 语法
                if (strpos($adapter, 'Pgsql') !== false) {
                    // PostgreSQL: 使用双引号，移除 INT(10) 的长度限制（PgSQL不支持）
                    $pgAttr = str_replace('INT(10)', 'INTEGER', $colAttr);
                    $sql = 'ALTER TABLE "' . $prefix . 'contents" ADD COLUMN "' . $colName . '" ' . $pgAttr . ';';
                } else {
                    // MySQL / SQLite: 保持原有语法 (使用反引号)
                    $sql = 'ALTER TABLE `' . $prefix . 'contents` ADD `' . $colName . '` ' . $colAttr . ';';
                }

                $db->query($sql);
            } catch (Typecho_Db_Exception $e) {
                // 忽略错误（列已存在或并发建列冲突）
            }
        }
    }
}

/**
 * 设置文章阅读量
 *
 * @param object $archive 文章
 * @return int 返回阅读量
 */
function postViews($archive) {
    // 获取文章的 cid
    $cid = $archive->cid;
    $db = Typecho_Db::get();

    // Typecho 的 archive 查询使用 SELECT * 已包含 views 字段，直接使用避免额外查询
    if (isset($archive->views)) {
        $views = (int)$archive->views;
    } else {
        $row = $db->fetchRow($db->select('views')->from('table.contents')->where('cid = ?', $cid));
        $views = (int)$row['views'];
    }

    // 仅在文章详情页才更新阅读计数，列表页只读不写
    if ($archive->is('single')) {
        // 获取阅读 cookie
        $cookieViews = Typecho_Cookie::get('extend_contents_views');
        if (empty($cookieViews)) {
            $cookieViews = array();
        } else {
            $cookieViews = explode(',', $cookieViews);
        }
        // 如果 cookie 不存在
        if (!in_array($cid, $cookieViews)) {
            // 阅读量 +1
            $db->query($db->update('table.contents')->rows(array('views' => $views + 1))->where('cid = ?', $cid));
            $cookieViews[] = $cid;
            // 只保留最近 200 个（阅读 cookie 保持会话级，不延长有效期）
            $cookieViews = array_slice($cookieViews, -200);
            $cookieViews = implode(',', $cookieViews);
            // 写入阅读 cookie
            Typecho_Cookie::set('extend_contents_views', $cookieViews);
            // 返回的最终阅读量 +1
            $views++;
        }
    }
    return $views;
}

/**
 * 检测是否是QQ邮箱
 *
 * @param string $email 邮箱
 * @return bool
 */
function isQQEmail($email) {
    $re = '/^\d{6,11}\@qq\.com$/';
    preg_match($re, $email, $result);
    if (count($result)) {
        return true;
    }
    return false;
}

/**
 * 获取QQ头像，直接输出
 *
 * @param string $email 邮箱
 * @param string $name 称呼，用于 img 的 alt
 * @param int $size 头像尺寸
 * @return void
 */
function QQAvatar($email, $name, $size) {
    $qq = str_replace('@qq.com', '', $email);
    $imgUrl = 'https://q2.qlogo.cn/headimg_dl?dst_uin=' . $qq . '&spec=' . $size;
    echo '<img src="' . $imgUrl . '" alt="' . $name . '" class="avatar" width="' . $size . '" height="' . $size . '" loading="lazy" decoding="async">';
}

/**
 * 评论时间格式化
 *
 * @param int $date 日期时间戳
 * @param string $options 评论日期格式设置
 * @return string 返回格式化后的日期
 */
function commentDateFormat($date, $options = 'format1') {
    // 中文日期
    if ($options == 'format1') {
        return date('Y年m月d日 H:i', $date);
    }
    // - 分隔的日期
    if ($options == 'format2') {
        return date('Y-m-d H:i', $date);
    }
    // 英文日期
    if ($options == 'format3') {
        return date('F jS, Y \a\t h:i a', $date);
    }
    // 时间间隔
    if ($options == 'format4') {
        if ($GLOBALS['language'] == 'en') {
            // 英文
            return formatTimeDifferenceEN($date);
        }else {
            // 中文
            return formatTimeDifferenceZH($date);
        }
    }
}

/**
 * 计算时间间隔
 *
 * @param int $timestamp 时间戳
 * @return string 返回中文的时间间隔
 */
function formatTimeDifferenceZH($timestamp) {
    $timestamp = time() - $timestamp;
    if ($timestamp < 1) {
        return '1秒前';
    }else if ($timestamp < 60) {
        return $timestamp . '秒前';
    }else if ($timestamp > 60 && $timestamp < 3600) {
        return round($timestamp / 60, 0) . '分钟前';
    }else if ($timestamp > 3600 && $timestamp < 86400) {
        return round($timestamp / 3600, 0) . '小时前';
    }else {
        return round($timestamp / 86400, 0) . '天前';
    }
}

/**
 * 计算时间间隔（英文）
 *
 * @param int $timestamp 时间戳
 * @return string 返回英文的时间间隔
 */
function formatTimeDifferenceEN($timestamp) {
    $diff = time() - $timestamp;

    if ($diff < 60) {
        return $diff == 1 ? "1 second ago" : "$diff seconds ago";
    }

    $minutes = floor($diff / 60);
    if ($minutes < 60) {
        return $minutes == 1 ? "1 minute ago" : "$minutes minutes ago";
    }

    $hours = floor($minutes / 60);
    if ($hours < 24) {
        return $hours == 1 ? "1 hour ago" : "$hours hours ago";
    }

    $days = floor($hours / 24);
    return $days == 1 ? "1 day ago" : "$days days ago";
}

/**
 * 获取文章头图显示设置
 *
 * @param object $t 文章
 * @param array $options 全局的文章头图显示设置
 * @param string $defaultImageUrl 默认头图 URL
 * @return false|string 文章头图 URL
 */
function headerImageDisplay($t, $options, $defaultImageUrl) {
    // 在文章列表和文章页显示文章头图
    if ($t->fields->headerImgDisplay == 'post-page-list') {
        return postImg($t, $defaultImageUrl);
    }
    // 在文章列表显示文章头图
    if ($t->fields->headerImgDisplay == 'post-list' && $t->is('index') or $t->fields->headerImgDisplay == 'post-list' && $t->is('archive')) {
        return postImg($t, $defaultImageUrl);
    }
    // 在文章页显示文章头图
    if ($t->fields->headerImgDisplay == 'post-page' && $t->is('post')) {
        return postImg($t, $defaultImageUrl);
    }
    // 使用系统文章头图设置
    if ($t->fields->headerImgDisplay == 'default' or $t->fields->headerImgDisplay == null) {
        // 在首页文章列表显示文章头图
        if (is_array($options) && in_array('home', $options) && $t->is('index')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在分类页、标签页、日期归档页显示文章头图
        if (is_array($options) && in_array('home', $options) && $t->is('archive')) {
            return postImg($t, $defaultImageUrl);
        }
        // 在文章页和独立页显示文章头图（注意括号：独立页同样受复选框约束）
        if (is_array($options) && in_array('post', $options) && ($t->is('post') || $t->is('page'))) {
            return postImg($t, $defaultImageUrl);
        }
    }
    // 不显示文章头图
    if ($t->fields->headerImgDisplay == 'hide') return false;
    return false;
}

/**
 * 根据设置获取文章头图
 *
 * @param object $a 文章
 * @param string $defaultUrl 默认文章头图 URL
 * @return false|mixed 文章头图 URL
 */
function postImg($a, $defaultUrl) {
    // 手动输入文章头图
    if ($a->fields->imageSource == 'url' && $a->fields->thumb != '') {
        $img = $a->fields->thumb;
    }
    // 随机文章头图
    elseif ($a->fields->imageSource == 'default') {
        $img = randomHeaderImage($defaultUrl);
    }
    // 默认使用第一张图片作为文章头图
    else {
        $img = getPostImg($a);
    }

    if (!$img) return false;

    // 列表页：开启 WebP 缩略图时，非 WebP 图片生成裁剪的 WebP 缓存
    if (isArchivePage() && Helper::options()->webpThumbnail === 'on') {
        $thumb = generateCropWebP($img, 320, 85, 4.0/3.0);
        return $thumb ? $thumb : $img;
    }

    return $img;
}

/**
 * 判断当前是否为列表页（首页/归档/分类/标签）
 *
 * @return bool
 */
function isArchivePage() {
    if (!isset($GLOBALS['page'])) return false;
    return in_array($GLOBALS['page'], array('index', 'archive'));
}

/**
 * 生成裁剪的 WebP 缩略图
 *
 * @param string $srcUrl 原图 URL
 * @param int $width 输出宽度（默认 480px）
 * @param int $quality WebP 质量（默认 75）
 * @param float $ratio 裁剪宽高比（默认 3:2）
 * @return string|false 缩略图 URL 或 false
 */
function generateCropWebP($srcUrl, $width = 480, $quality = 75, $ratio = 3.0/2.0) {
    // 只处理本站上传的图片
    $siteUrl = Helper::options()->siteUrl;
    // 相对路径补全为完整 URL
    if (strpos($srcUrl, '//') !== 0 && strpos($srcUrl, '/') === 0) {
        $srcUrl = rtrim($siteUrl, '/') . $srcUrl;
    }
    if (strpos($srcUrl, $siteUrl) === false) {
        return false;
    }

    $ext = strtolower(pathinfo(parse_url($srcUrl, PHP_URL_PATH), PATHINFO_EXTENSION));

    // 获取绝对路径
    $relativePath = str_replace($siteUrl, '', $srcUrl);
    $relativePath = strtok($relativePath, '?');
    $absPath = __TYPECHO_ROOT_DIR__ . '/' . ltrim($relativePath, '/');

    if (!file_exists($absPath)) {
        return false;
    }

    // 缩略图存到主题目录：assets/cache/thumbs/
    $themeDir = __DIR__ . '/..';
    $thumbDir = $themeDir . '/assets/cache/thumbs';
    $thumbName = md5($srcUrl) . '_w' . $width . '_r' . str_replace('.', '_', number_format($ratio, 2)) . '.webp';
    $thumbPath = $thumbDir . '/' . $thumbName;
    $thumbUrl = Helper::options()->themeUrl . '/assets/cache/thumbs/' . $thumbName;

    // 缓存命中且比原图新
    if (file_exists($thumbPath) && filemtime($thumbPath) >= filemtime($absPath)) {
        return $thumbUrl;
    }

    // 创建 thumbs 目录
    if (!is_dir($thumbDir)) {
        mkdir($thumbDir, 0755, true);
    }

    // 读取原图
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $srcImg = @imagecreatefromjpeg($absPath);
            break;
        case 'png':
            $srcImg = @imagecreatefrompng($absPath);
            break;
        case 'gif':
            $srcImg = @imagecreatefromgif($absPath);
            break;
        case 'webp':
            $srcImg = @imagecreatefromwebp($absPath);
            break;
        default:
            return false;
    }

    if (!$srcImg) return false;

    $srcW = imagesx($srcImg);
    $srcH = imagesy($srcImg);

    // 按指定比例中心裁剪
    $height = (int)round($width / $ratio);
    $targetRatio = $ratio;
    $srcRatio = $srcW / $srcH;

    // 根据源图宽高比与目标比例的关系，决定裁剪两侧还是上下
    if ($srcRatio > $targetRatio) {
        // 源图更宽，裁剪左右两侧
        $cropW = (int)round($srcH * $targetRatio);
        $cropH = $srcH;
        $cropX = (int)(($srcW - $cropW) / 2);
        $cropY = 0;
    } else {
        // 源图更高，裁剪上下
        $cropW = $srcW;
        $cropH = (int)round($srcW / $targetRatio);
        $cropX = 0;
        $cropY = (int)(($srcH - $cropH) / 2);
    }

    $thumbImg = imagecreatetruecolor($width, $height);
    imagealphablending($thumbImg, false);
    imagesavealpha($thumbImg, true);
    $transparent = imagecolorallocatealpha($thumbImg, 0, 0, 0, 127);
    imagefill($thumbImg, 0, 0, $transparent);

    imagecopyresampled($thumbImg, $srcImg, 0, 0, $cropX, $cropY, $width, $height, $cropW, $cropH);

    $result = imagewebp($thumbImg, $thumbPath, $quality);

    // PHP 8.0+ GdImage 析构自动释放，imagedestroy 自 8.5 起作废，不再调用
    return $result ? $thumbUrl : false;
}

/**
 * 获取文章的第一张图片
 *
 * @param object $archive 文章
 * @return false|string 返回文章头图或 false
 */
function getPostImg($archive) {
    // 从数据库直接取原始内容，避免 HyperDown 解析超时
    $db = Typecho_Db::get();
    $row = $db->fetchRow($db->select('text')->from('table.contents')->where('cid = ?', $archive->cid)->limit(1));
    $text = $row ? $row['text'] : '';
    if (!$text) return false;
    // 优先匹配 HTML img 标签
    if (preg_match('/<img[^>]+src=["\']([^"\']+)["\']/i', $text, $match)) {
        return $match[1];
    }
    // 回退匹配 Markdown 图片语法 ![alt](url)
    if (preg_match('/!\[[^\]]*\]\(([^)\s]+)\)/i', $text, $match)) {
        return $match[1];
    }
    return false;
}

/**
 * 获取随机文章头图
 *
 * @param string $imgUrl 默认文章头图URL
 * @return false|string 返回文章头图URL
 */
function randomHeaderImage($imgUrl) {
    if ($imgUrl == null or $imgUrl == '') return false;
    // 把 URL 按行拆分为数组
    $imgUrl = explode(PHP_EOL, $imgUrl);
    // 删除因为空行生成的数组空值
    $imgUrl = array_filter($imgUrl);
    // 如果只有一个 URL 就直接返回 URL
    if (count($imgUrl) < 2) return $imgUrl[0];
    // 随机返回一个 URL
    return $imgUrl[mt_rand(0, count($imgUrl) - 1)];
}

/**
 * 获取文章列表的文章头图样式设置
 *
 * @param string $postStyle 单篇文章的头图样式
 * @param string $optionsStyle 全局文章头图样式
 * @return string 返回文章头图样式设置
 */
function getPostListHeaderImageStyle($postStyle, $optionsStyle) {
    if ($postStyle == 'max' or $postStyle == 'mini') {
        return $postStyle;
    }
    if ($postStyle == 'default' or $postStyle == null) {
        if ($optionsStyle == 'max' or $optionsStyle == 'mini') {
            return $optionsStyle;
        }
        return 'max';
    }
    return 'max';
}

/**
 * 获取父分类的名称
 *
 * @param int $categoryId 分类id
 * @return string 返回父分类的名称
 */
function getParentCategory($categoryId) {
    $db = Typecho_Db::get();
    $category = $db->fetchRow($db->select()->from('table.metas')->where('mid = ?', $categoryId));
    return $category['name'];
}

/**
 * 计算两个时间之间相差的天数
 *
 * @param int $time1 时间戳
 * @param int $time2 时间戳
 * @return false|float 返回天数
 */
function getDays($time1, $time2) {
    return floor(($time2 - $time1) / 86400);
}

/**
 * 根据文章内的标题生成目录
 *
 * @param string $content 文章内容
 * @return array 返回文章内容和目录
 */
function articleDirectory($content) {
    $re = '#<h(\d)(.*?)>(.*?)</h\d>#im';
    preg_match_all($re, $content, $result);
    if (!is_array($result) or count($result[0]) < 1) {
        return array('content' => $content, 'directory' => null);
    }

    $treeList = array();
    $id = 1;
    foreach ($result[1] as $i => $level) {
        $treeList[$id] = array(
            'id' => $id,
            'parent_id' => 0,
            'level' => $level,
            'name' => trim(strip_tags($result[3][$i])),
            'rand' => mt_rand(1000, 9999)
        );
        $id ++;
    }

    for ($i = 2;$i <= count($treeList);$i ++) {
        $item = $treeList[$i];
        $prevItem = $treeList[$i - 1];
        if ($item['level'] == $prevItem['level']) {
            $treeList[$i]['parent_id'] = $prevItem['parent_id'];
            continue;
        }
        if ($item['level'] > $prevItem['level']) {
            $treeList[$i]['parent_id'] = $prevItem['id'];
            continue;
        }
        $parentId = 0;
        while ($item['level'] <= $prevItem['level']) {
            $parentId = $prevItem['parent_id'];
            if (!isset($treeList[($prevItem['id'] - 1)])) {
                break;
            }
            $prevItem = $treeList[($prevItem['id'] - 1)];
        }
        $treeList[$i]['parent_id'] = $parentId;
    }

    $tree = array();
    foreach ($treeList as $item) {
        if ($item[ 'parent_id' ] != 0 && !isset($treeList[$item['parent_id']])) {
            continue;
        }
        if (isset($treeList[$item['parent_id']])) {
            $treeList[$item['parent_id']]['children'][] = &$treeList[$item['id']];
        } else {
            $tree[] = &$treeList[$item['id']];
        }
    }

    $GLOBALS['directory'] = $treeList;
    $GLOBALS['directoryIndex'] = 1;
    $content = preg_replace_callback($re, function ($matches) {
        $name = urlencode(strip_tags($matches[3]));
        $span = '<span class="title-position" data-title="p-' . $GLOBALS['directory'][$GLOBALS['directoryIndex']]['id'] . '" id="p-' . $GLOBALS['directory'][$GLOBALS['directoryIndex']]['id'] . '"></span>' . $matches[0];
        $GLOBALS['directoryIndex'] ++;
        return $span;
    }, $content);

    return array(
        'content' => $content,
        'directory' => renderArticleDirectory($tree, '')
    );
}

/**
 * 生成目录 HTML
 *
 * @param $tree
 * @param $parent
 * @return string 返回文章目录HTML
 */
function renderArticleDirectory($tree, $parent = '') {
    $index = 1;
    $ariaLabel = $tree[0]['parent_id'] == 0?'aria-label="' . $GLOBALS['t']['sidebar']['tableOfContents'] . '"':'';
    $htmlStr = '<ul class="article-directory"' . $ariaLabel . '>';
    foreach ($tree as $item) {
        $num = $parent == ''?$index:$parent . '.' . $index;
        $htmlStr .= sprintf('<li><a rel="bookmark" data-directory="%s" class="directory-link" href="#%s">%s</a></li>', 'p-' . $item['id'], 'p-' . $item['id'], '<span class="mr-2 directory-num">' . $num . '</span>' . $item['name']);
        if (isset($item['children']) && count($item['children']) > 0) {
            $htmlStr .= renderArticleDirectory($item['children'], $num);
        }
        $index ++;
    }
    $htmlStr .= '</ul>';
    return $htmlStr;
}

/**
 * 检测是否是 IE
 *
 * @return bool IE 返回 true，不是 IE 返回 false
 */
function isIE() {
    $agent = $_SERVER['HTTP_USER_AGENT'];
    if (preg_match('/MSIE/i', $agent) || preg_match('/Trident/i', $agent)) {
        return true;
    }
    return false;
}

/**
 * 把图片的 src 替换为 data-src，用于图片懒加载
 *
 * @param string $content 文章内容
 * @return string 替换后的文章内容
 */
function replaceImgSrc($content) {
    $pattern = '/<img(.*?)src(.*?)=(.*?)"(.*?)">/i';
    $replacement = '<img$1data-src$3="$4"$5 class="load-img">';
    return preg_replace($pattern, $replacement, $content);
}

/**
 * 获取 Gravatar 头像，直接输出 img
 *
 * @param string $email 邮箱
 * @param int $size 头像尺寸
 * @param string $gravatarUrl 自定义 gravatarUrl 源
 * @param string $alt 头像图片描述
 * @return void
 */
function gravatar($email, $size, $gravatarUrl = '', $alt = '') {
    $url = $gravatarUrl . md5(strtolower(trim($email))) . '?s=' . $size;
    if ($gravatarUrl == '' or $gravatarUrl == null) {
        $url = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=' . $size;
    }
    echo '<img src="' . $url . '" alt="' . $alt . '" class="avatar" width="' . $size . '" height="' . $size . '" loading="lazy" decoding="async" />';
}

/**
 * 获取网站管理员的用户信息
 *
 * @return object 管理员用户信息
 */
function getAdminInfo() {
    $db = Typecho_Db::get();
    $userInfo = $db->fetchRow($db->select('mail', 'url', 'screenName', 'created')->from('table.users')->where('group = ?', 'administrator'));
    return $userInfo;
}

/**
 * 获取文章列表显示设置
 *
 * @param string $option 文章列表的全局设置
 * @param string $postOption 单篇文章的列表设置
 * @return mixed|string 文章列表显示设置
 */
function postListStyle($option, $postOption) {
    // 判断单篇文章的列表显示设置
    if ($postOption == 'summary' or $postOption == 'fullText' or $postOption == 'wechat') {
        return $postOption;
    }
    // 判断列表全局设置
    if ($option == 'fullText' or $option == 'summary' or $option == 'wechat') {
        return $option;
    }
    // 如果出现异常就默认显示文章摘要和
    return 'summary';
}

/**
 * 根据秒数偏移量设置全局时区
 * * @param int|string $offset Typecho 格式的时区偏移量 (例如: "28800" 或 28800)
 * @return void
 */
function setTimezoneByOffset($offset) {
    // 强制转换为整数
    $offset = (int) $offset;

    // 尝试根据偏移量获取合法的时区名称 (例如 "Asia/Shanghai" 或 "Etc/GMT-8")
    $timezone_name = timezone_name_from_abbr('', $offset, 0);
    // 如果获取失败（极少数情况），或者获取到的是 false
    if ($timezone_name === false) {
        // 手动回退逻辑：构建 Etc/GMT 时区
        $hours = $offset / 3600;
        if ($hours > 0) {
            $timezone_name = 'Etc/GMT-' . $hours;
        } else {
            $timezone_name = 'Etc/GMT+' . abs($hours);
        }
    }

    // 设置全局时区
    @date_default_timezone_set($timezone_name);
}

/**
 * 文章内容分页
 *
 * @param string $content 文章的 HTML 内容
 * @return array 分页后的内容数组
 */
function splitArticleContent($content) {
    $pattern = '/<(pre|code)\b[^>]*>.*?<\/\1>(*SKIP)(*FAIL)|<p>\s*\[-page-\]\s*<\/p>|\[-page-\]/is';
    // 使用 preg_split 进行分割
    return preg_split($pattern, $content);
}

/**
 * 生成分页
 *
 * @param object $archive 包含 pageNav 方法的 typecho 文章或评论对象
 * @param string $previousPageTitle 用于上一页 title 的文字
 * @param string $nextPageTitle 用于下一页 title 的文字
 * @return bool 是否存在下一页
 */
function paginate($archive, $previousPageTitle, $nextPageTitle) {
    ob_start();
    // typecho 分页
    $archive->pageNav($previousPageTitle, $nextPageTitle, 1, '...', array(
        'wrapTag' => 'div',
        'wrapClass' => 'pagination',
        'itemTag' => 'span',
        'textTag' => 'span',
        'currentClass' => 'active',
        'prevClass' => 'prev-page',
        'nextClass' => 'next-page'
    ));
    $content = ob_get_contents();
    ob_end_clean();

    // 如果没有分页则不输出
    if (empty($content)) {
        return false;
    }

    echo $content;
    // 下一页链接存在即还有更多
    return strpos($content, 'next-page') !== false;
}

/**
 * 为文章内容中的站外链接添加 target="_blank" 与 rel="noopener"
 *
 * 遍历文章内容中的 <a> 链接，当链接指向本站以外的站点时，
 * 自动添加 target="_blank"（新窗口打开）与 rel="noopener"（防止新窗口劫持）；
 * 本站链接（相对链接、锚点链接、同域名链接）以及 <pre> / <code> 代码块内的链接不处理。
 *
 * @param string $content 文章内容的 HTML 字符串
 * @param string $siteUrl 本站地址，例如 https://example.com/
 * @return string 处理后的 HTML 字符串
 */
function addExternalLinkAttributes($content, $siteUrl) {
    // 没有链接时直接返回原内容
    if (empty($content) || strpos($content, '<a') === false) {
        return $content;
    }

    // 匹配 <pre> / <code> 代码块（原样保留）或 <a> 开始标签
    $pattern = '/(<pre\b[^>]*>.*?<\/pre>|<code\b[^>]*>.*?<\/code>)|<a\b([^>]*)>/is';

    return preg_replace_callback($pattern, function ($matches) use ($siteUrl) {
        // 命中代码块时直接返回
        if (!empty($matches[1])) {
            return $matches[1];
        }

        $attrs = $matches[2];

        // 提取 href 属性
        if (preg_match('/\bhref\s*=\s*(["\'])(.*?)\1/i', $attrs, $hrefMatches)) {
            $href = trim($hrefMatches[2]);
        } elseif (preg_match('/\bhref\s*=\s*([^\s>]+)/i', $attrs, $hrefMatches)) {
            $href = trim($hrefMatches[1]);
        } else {
            return $matches[0];
        }

        // 本站链接不处理
        if (isInternalLink($href, $siteUrl)) {
            return $matches[0];
        }

        // 站外链接：添加 target="_blank"（已存在时不重复添加）
        if (!preg_match('/\btarget\s*=/i', $attrs)) {
            $attrs .= ' target="_blank"';
        }

        // 站外链接：添加 / 合并 rel="noopener"
        if (preg_match('/\brel\s*=\s*(["\'])(.*?)\1/i', $attrs, $relMatches)) {
            $relParts = preg_split('/\s+/', trim($relMatches[2]));
            if (!in_array('noopener', $relParts)) {
                $relParts[] = 'noopener';
                $attrs = str_replace($relMatches[0], 'rel=' . $relMatches[1] . implode(' ', $relParts) . $relMatches[1], $attrs);
            }
        } else {
            $attrs .= ' rel="noopener"';
        }

        return '<a' . $attrs . '>';
    }, $content);
}

/**
 * 判断链接是否为本站链接
 *
 * 锚点链接、相对路径、非 http(s) 协议的链接（如 mailto、tel）均视为本站链接；
 * http(s) 绝对链接与协议相对链接（//xxx.com）会与本站域名比较。
 *
 * @param string $href 链接地址
 * @param string $siteUrl 本站地址
 * @return bool 为本站链接时返回 true
 */
function isInternalLink($href, $siteUrl) {
    // 空地址、锚点视为本站链接
    if ($href === '' || $href[0] === '#') {
        return true;
    }

    // 协议相对地址（//xxx.com）补协议后比较域名
    if (strpos($href, '//') === 0) {
        $href = 'http:' . $href;
    } elseif (!preg_match('#^https?://#i', $href)) {
        // 相对路径以及 mailto、tel 等非 http(s) 协议视为本站链接
        return true;
    }

    // 解析本站域名
    $siteHost = parse_url($siteUrl, PHP_URL_HOST);
    if (empty($siteHost)) {
        return true;
    }
    $siteHost = strtolower($siteHost);

    // 解析链接域名
    $linkHost = parse_url($href, PHP_URL_HOST);
    if (empty($linkHost)) {
        return true;
    }
    $linkHost = strtolower($linkHost);

    // 比较域名，忽略 www 前缀差异
    if (strpos($siteHost, 'www.') === 0) {
        $siteHost = substr($siteHost, 4);
    }
    if (strpos($linkHost, 'www.') === 0) {
        $linkHost = substr($linkHost, 4);
    }
    return $linkHost === $siteHost;
}

/**
 * 输出自定义代码高亮 CSS
 *
 * 输入 highlight.js 配色 CSS 的 URL 则输出 <link> 引用，
 * 直接粘贴 CSS 代码则内联输出（会先剥掉可能误粘的 style 标签，并做转义防 XSS）。
 *
 * @param string $input 用户输入的 URL 或 CSS 代码
 * @return void
 */
function outputCustomHighlightCSS($input) {
    $input = trim($input ?? '');
    if (empty($input)) {
        return;
    }
    // URL：输出 link 引用
    if (preg_match('/^(https?:)?\/\/[^\s{}]+$/i', $input) || preg_match('/^\/[^\s{}]+$/i', $input)) {
        echo '<link rel="stylesheet" href="' . htmlspecialchars($input, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    } else {
        // CSS 代码：剥掉 style 标签后内联输出
        $input = preg_replace('/<\/?style[^>]*>/i', '', $input);
        echo "<style>\n" . trim($input) . "\n</style>\n";
    }
}

/**
 * 分类占比环形图（SVG 描边分段，无渐变、无 JS）
 *
 * @param array $items 每项为 array('name' => 名称, 'value' => 数量)
 * @return string 环形图 + 图例 HTML
 */
function donutChart($items) {
    $total = 0;
    foreach ($items as $it) $total += intval($it['value']);
    if ($total < 1) return '';
    $palette = array('#ff006e', '#0090c1', '#e07b00', '#5a9e00', '#8b5cf6', '#0e9f8a');
    $r = 70;
    $c = 2 * M_PI * $r;
    $segs = '';
    $legend = '';
    $acc = 0;
    $i = 0;
    $label = array();
    foreach ($items as $it) {
        $v = intval($it['value']);
        if ($v < 1) continue;
        $pct = $v / $total * 100;
        $len = $pct / 100 * $c;
        $color = $palette[$i % count($palette)];
        $segs .= '<circle cx="90" cy="90" r="' . $r . '" fill="none" stroke="' . $color . '" stroke-width="28"'
            . ' stroke-dasharray="' . round($len, 1) . ' ' . round($c, 1) . '"'
            . ' stroke-dashoffset="' . round(-$acc / 100 * $c, 1) . '" transform="rotate(-90 90 90)"/>';
        $acc += $pct;
        $legend .= '<li><i style="background:' . $color . '"></i><span>' . htmlspecialchars($it['name']) . '</span><b>' . $v . '（' . round($pct, 1) . '%）</b></li>';
        $label[] = $it['name'] . $v . '篇';
        $i++;
    }
    return '<div class="donut-wrap"><div class="donut-svg" role="img" aria-label="分类占比：' . htmlspecialchars(implode('，', $label)) . '">'
        . '<svg viewBox="0 0 180 180" width="180" height="180" aria-hidden="true">'
        . '<circle cx="90" cy="90" r="' . $r . '" fill="none" style="stroke:var(--surface2)" stroke-width="28"/>' . $segs . '</svg>'
        . '<div class="donut-hole"><b>' . $total . '</b></div></div>'
        . '<ul class="donut-legend">' . $legend . '</ul></div>';
}

/**
 * 更新日历热力图（GitHub 风格，纯 HTML+CSS，无 JS）
 *
 * @param array $rows 每项为 array(日期 Y-m-d, 数量)
 * @param int $days 回溯天数
 * @return string 热力图 HTML
 */
function heatCalendar($rows, $days = 240) {
    $map = array();
    $max = 0;
    foreach ((array)$rows as $r) {
        $map[$r[0]] = intval($r[1]);
        if (intval($r[1]) > $max) $max = intval($r[1]);
    }
    $end = strtotime('today');
    $start = $end - ($days - 1) * 86400;
    // 对齐到周一（date('N') 1=周一）
    $pad = date('N', $start) - 1;
    $html = '<div class="heat" role="img">';
    for ($k = 0; $k < $pad; $k++) $html .= '<span class="heat-day blank"></span>';
    for ($t = $start; $t <= $end; $t += 86400) {
        $d = date('Y-m-d', $t);
        $c = isset($map[$d]) ? $map[$d] : 0;
        $lv = ($c < 1 || $max < 1) ? 0 : max(1, min(4, (int)ceil($c / $max * 4)));
        $html .= '<span class="heat-day lv' . $lv . '" title="' . $d . '：' . $c . '"></span>';
    }
    return $html . '</div>';
}

/**
 * 为文章中的表格添加响应式包裹
 *
 * @param string $html 原始文章 HTML
 * @return string 处理后的 HTML
 */
function tableWrap($html) {
    // 没有表格直接返回原内容
    if (empty($html) || strpos($html, '<table') === false) {
        return $html;
    }

    // 用 <div class="table-wrap"> 包裹每个 <table>（横向滚动，避免撑破布局）
    $html = preg_replace('/<table\b/i', '<div class="table-wrap"><table', $html);
    $html = preg_replace('/<\/table>/i', '</table></div>', $html);

    return $html;
}

/**
 * 给文章内的标签添加样式
 *
 * @param object $post 文章对象
 * @return void
 */
function postTags($post) {
    // 拦截输出
    ob_start();
    $post->tags(' ', true, $GLOBALS['t']['post']['noneTag']);
    $content = ob_get_contents();
    ob_end_clean();
    // 给标签链接添加 class
    $content = str_replace('<a href=', '<a class="tag" href=', $content);

    echo $content;
}
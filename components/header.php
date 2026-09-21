<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 安全响应头（静态资源缓存由 Nginx/CDN 层负责，此处只管 HTML）
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

// 让主题使用的时区跟随 Typecho 设置的时区
setTimezoneByOffset($this->options->timezone);

// 解析主题配色：cookie 优先，其次后台设置；auto 由头部内联脚本按系统修正
$themeOpt = $this->options->themeColor;
if (isset($_COOKIE['themeColor']) && ($_COOKIE['themeColor'] == 'light-color' || $_COOKIE['themeColor'] == 'dark-color')) {
    $themeOpt = $_COOKIE['themeColor'];
}
$GLOBALS['color'] = $themeOpt;
$themeMode = $themeOpt == 'dark-color' ? 'dark' : 'light';
$themeAuto = $themeOpt == 'auto-color' ? '1' : '0';

// 代码高亮主题 class（highlight.css 负责 var 定义外的配色）
$codeThemeColor = $this->options->codeHighlight != 'enable-highlight' ? 'code-theme-none' : $this->options->codeThemeColor;

// 导航栏自定义链接
$navLinks = null;
if ($this->options->navLinks) $navLinks = json_decode($this->options->navLinks, true);

$bodyClass = array($codeThemeColor, $this->options->codeHighlight, $GLOBALS['color']);
$bodyClass = implode(' ', $bodyClass);

// 静态资源指纹（CDN 长缓存用 ?v=）
$themeDir = __DIR__ . '/..';
$v = function ($f) use ($themeDir) {
    $p = $themeDir . '/' . $f;
    return $f . '?v=' . (is_file($p) ? filemtime($p) : '1');
};
$themeUrl = rtrim($this->options->themeUrl, '/');
$page = $GLOBALS['page'] ?? '';
?>

<!doctype html>
<html lang="<?php echo $GLOBALS['language']; ?>" data-theme="<?php echo $themeMode; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <meta name="color-scheme" content="<?php echo $themeMode; ?>">
    <meta name="theme-color" content="<?php echo $themeMode == 'dark' ? '#141417' : '#ffffff'; ?>">
    <link rel="dns-prefetch" href="https://www.gravatar.com">
    <link rel="preconnect" href="https://www.gravatar.com" crossorigin>
    <?php if ($this->is('search') && $this->options->searchPageNoindex == 'show'): ?>
        <meta name="robots" content="noindex, follow">
    <?php endif; ?>
    <?php if ($this->is('date') && $this->options->dateArchivePageNoindex == 'show'): ?>
        <meta name="robots" content="noindex, follow">
    <?php endif; ?>
    <?php if ($this->is('author') && ($this->options->authorArchivePageNoindex ?: 'show') == 'show'): ?>
        <meta name="robots" content="noindex, follow">
    <?php endif; ?>
    <?php if ($this->is('index')): ?>
    <link rel="canonical" href="<?php $this->options->siteUrl(); ?>">
    <?php elseif (!$this->is('single') && $page != '404'): ?>
    <link rel="canonical" href="<?php echo $this->archiveUrl; ?>">
    <?php endif; ?>
    <title><?php
        $this->archiveTitle(array(
            'category' => $GLOBALS['t']['archive']['postsUnderTheCategory'],
            'search' => $GLOBALS['t']['archive']['postsContainingTheKeyword'],
            'tag' => $GLOBALS['t']['archive']['postsTagged'],
            'author' => $GLOBALS['t']['archive']['postsByAuthor']
        ), '', ' - ');
        ?><?php $this->options->title(); ?><?php if ($this->is('index')) echo $this->options->tagline; ?></title>
    <?php if ($this->is('post') && $this->fields->keywords or $this->fields->summaryContent): ?>
        <?php
        $metaContent = array();
        if ($this->fields->keywords) $metaContent['keywords'] = $this->fields->keywords;
        if ($this->fields->summaryContent) $metaContent['description'] = $this->fields->summaryContent;
        $metaContent = urldecode(http_build_query($metaContent));
        $this->header($metaContent);
        ?>
    <?php else: ?>
        <?php $this->header(); ?>
    <?php endif; ?>
    <link rel="icon" href="<?php echo $this->options->logoUrl?$this->options->logoUrl:$this->options->siteUrl . 'favicon.ico'; ?>" type="image/x-icon">
    <?php if ($themeAuto === '1'): ?>
    <script>
    (function(){try{var m=matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';document.documentElement.dataset.theme=m;var meta=document.querySelector('meta[name="color-scheme"]');if(meta)meta.content=m;}catch(e){}})();
    </script>
    <?php endif; ?>
    <!--关键 CSS 内联：首屏骨架（导航+卡片），避免白屏；包进最弱 layer，正式样式始终优先-->
    <style>
    @layer critical{
    *,*::before,*::after{box-sizing:border-box}body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI","PingFang SC","Microsoft YaHei",sans-serif;line-height:1.7}
    .wrap{max-width:1240px;margin-inline:auto;padding-inline:15px}
    .topbar{position:sticky;top:0;z-index:50;background:#000;color:#fff;border-bottom:2px solid #000}
    .topbar::after{content:"";display:block;height:4px;background:#ff006e}
    .navbar{display:flex;align-items:center;gap:12px;padding:10px 0;flex-wrap:wrap}
    .brand{font-weight:900;font-size:1.3rem;margin-right:auto;color:#fff}
    .grid{display:grid;gap:24px;padding-block:24px;grid-template-columns:1fr}
    @media(min-width:992px){.grid{grid-template-columns:minmax(0,2fr) minmax(0,1fr)}}
    .post-card,.post-card-wechat{background:#fff;border:2px solid #111;margin-bottom:16px;padding:16px;min-height:90px}
    html[data-theme="dark"] .post-card,html[data-theme="dark"] .post-card-wechat{background:#1b1b20;border-color:#f2f2f2}
    .post-card .card-title{font-size:1.25rem;line-height:1.4;margin:0}
    .post-card .card-row{display:flex;gap:16px;margin-top:10px}
    .post-card .card-thumb{width:180px;height:120px;flex-shrink:0}
    @media(max-width:575px){.post-card .card-thumb{width:120px;height:80px}}
    }
    </style>
    <link rel="stylesheet" href="<?php echo $themeUrl . '/' . $v('assets/css/style.css'); ?>">
    <?php if (in_array($page, array('post', 'page', 'page-data')) && $this->options->codeHighlight == 'enable-highlight'): ?>
        <?php if ($this->options->codeThemeColor == 'custom-code-theme'): ?>
            <?php outputCustomHighlightCSS($this->options->highlightJsCSS); ?>
        <?php else: ?>
    <link rel="stylesheet" href="<?php echo $themeUrl . '/' . $v('assets/css/highlight.css'); ?>">
        <?php endif; ?>
    <?php endif; ?>
    <?php localizeScript(); ?>
    <?php if ($this->options->cssCode): ?>
        <style type="text/css"><?php $this->options->cssCode(); ?></style>
    <?php endif; ?>
    <?php if ($this->options->headHTML): ?>
        <?php $this->options->headHTML(); ?>
    <?php endif; ?>
    <!--站内链接预渲染（替代 PJAX，原生无刷新体验增强）-->
    <script type="speculationrules">
    {"prerender": [{"source": "document", "where": {"href_matches": "<?php echo rtrim($this->options->siteUrl, '/'); ?>/*"}, "eagerness": "moderate"}]}
    </script>
    <?php if ($this->is('post')): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BlogPosting",
      "headline": "<?php $this->title(); ?>",
      "description": "<?php echo $this->fields->summaryContent ? htmlspecialchars($this->fields->summaryContent ?: $this->excerpt(200)) : htmlspecialchars($this->excerpt(200)); ?>",
      "author": {"@type": "Person", "name": "<?php $this->author(); ?>"},
      "publisher": {"@type": "Organization", "name": "<?php $this->options->title(); ?>"},
      "datePublished": "<?php echo date('c', $this->created); ?>",
      "dateModified": "<?php echo date('c', $this->modified); ?>"
    }
    </script>
    <?php elseif ($this->is('index')): ?>
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Blog",
      "name": "<?php $this->options->title(); ?>",
      "description": "<?php echo htmlspecialchars($this->options->description); ?>",
      "url": "<?php $this->options->siteUrl(); ?>"
    }
    </script>
    <?php endif; ?>
</head>
<body class="<?php echo $bodyClass; ?>" data-page="<?php echo $page; ?>" data-theme-url="<?php echo $themeUrl; ?>" data-sw="<?php echo $themeUrl . '/' . $v('assets/js/sw.js'); ?>">
<a class="skip-link" href="#main">跳转到正文内容</a>
<header class="topbar">
    <div class="wrap">
        <nav class="navbar" aria-label="<?php echo $GLOBALS['t']['header']['navigationMenu']; ?>">
            <?php if ($this->options->navLogoUrl): ?>
                <?php $navLogo = ($themeMode == 'dark' && $this->options->navDarkLogoUrl) ? $this->options->navDarkLogoUrl : $this->options->navLogoUrl; ?>
                <a class="brand" href="<?php $this->options->siteUrl(); ?>" title="<?php $this->options->title(); ?>">
                    <img id="nav-logo" src="<?php echo $navLogo; ?>" alt="<?php $this->options->title(); ?>" height="<?php $this->options->navLogoHeight(); ?>"<?php if ($this->options->navDarkLogoUrl): ?> data-logo-light="<?php $this->options->navLogoUrl(); ?>" data-logo-dark="<?php $this->options->navDarkLogoUrl(); ?>"<?php endif; ?>>
                </a>
            <?php else: ?>
                <a class="brand" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
            <?php endif; ?>
            <button type="button" id="theme-color-toggle" class="theme-btn" aria-label="<?php echo $GLOBALS['t']['sidebar']['themeColor']; ?>" title="<?php echo $GLOBALS['t']['sidebar']['themeColor']; ?>">
                <span id="theme-color-icon"><?php echo $themeMode == 'dark' ? '🌙' : '☀️'; ?></span>
            </button>
            <button class="nav-toggle" id="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-links">☰</button>
            <div class="nav-links" id="nav-links">
                <a href="<?php $this->options->siteUrl(); ?>" <?php if ($this->is('index')) echo 'aria-current="page"'; ?>><?php echo $GLOBALS['t']['header']['home']; ?></a>
                <?php $this->widget('Widget_Contents_Page_List')->to($pages); ?>
                <?php while($pages->next()): ?>
                    <a href="<?php $pages->permalink(); ?>" <?php if ($this->is('page', $pages->slug)) echo 'aria-current="page"'; ?>><?php $pages->title(); ?></a>
                <?php endwhile; ?>
                <?php if ($this->options->navLinks && is_array($navLinks)): ?>
                    <?php foreach ($navLinks as $link): ?>
                        <?php if (isset($link['menu']) && count($link['menu'])): ?>
                            <details class="nav-drop">
                                <summary><?php echo $link['name']; ?></summary>
                                <div class="nav-drop-menu">
                                    <?php foreach ($link['menu'] as $menuItem): ?>
                                        <a href="<?php echo $menuItem['url']; ?>"><?php echo $menuItem['name']; ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </details>
                        <?php else: ?>
                            <a href="<?php echo $link['url']; ?>"><?php echo $link['name']; ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                <form class="search-form" action="<?php $this->options->siteUrl(); ?>" method="post" role="search">
                    <input type="search" placeholder="<?php echo $GLOBALS['t']['header']['search']; ?>" required name="s" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>">
                    <button type="submit" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>"><i class="icon-search"></i></button>
                </form>
            </div>
        </nav>
    </div>
</header>

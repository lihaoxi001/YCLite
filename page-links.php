<?php
/**
 * 友情链接
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'page-links';

// 语言初始化
languageInit();

$linkArr = array();
//  是否包含内页链接
if ($this->options->pageLinks) {
    $linkArr[] = array(
        'title' => $GLOBALS['t']['linkPage']['linksOnDedicatedPageOnly'],
        'links' => json_decode($this->options->pageLinks)
    );
}
//  是否包含首页链接
if (
    is_array($this->options->linkPageOptions) &&
    in_array('showHomepageOnLinkPage', $this->options->linkPageOptions) &&
    $this->options->homeLinks
) {
    $linkArr[] = array(
        'title' => $GLOBALS['t']['linkPage']['linksOnHomepage'],
        'links' => json_decode($this->options->homeLinks)
    );
}
//  是否包含全站链接
if (
    is_array($this->options->linkPageOptions) &&
    in_array('showSitewideOnLinkPage', $this->options->linkPageOptions) &&
    $this->options->links
) {
    $linkArr[] = array(
        'title' => $GLOBALS['t']['linkPage']['linksOnAllPages'],
        'links' => json_decode($this->options->links)
    );
}
$this->need('components/header.php');
?>

<main class="wrap" id="main">
    <div class="grid">
        <div class="post-page">
            <?php if ($this->options->breadcrumb == 'on'): ?>
                <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="crumbs">
                    <ol>
                        <li>
                            <a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a>
                        </li>
                        <li tabindex="0" aria-current="page"><?php $this->title(); ?></li>
                    </ol>
                </nav>
            <?php endif; ?>
                <article class="post">
                    <header>
                        <h1 class="post-title">
                            <a href="<?php $this->permalink(); ?>" rel="bookmark"><?php $this->title(); ?></a>
                        </h1>
                    </header>
                    <?php $headerImg = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl); ?>
                    <?php if ($headerImg): ?>
                        <div class="header-img">
                            <a href="<?php $this->permalink(); ?>" aria-hidden="true" aria-label="文章头图" style="background-image: url(<?php echo $headerImg; ?>);" tabindex="-1"></a>
                        </div>
                    <?php endif; ?>
                    <div class="post-info">
                        <span title="<?php echo $GLOBALS['t']['post']['publicationDate']; ?>">
                                <i class="icon-calendar" aria-hidden="true"></i>
                                <time datetime="<?php echo date('c', $this->created); ?>"><?php echo postDateFormat($this->created); ?></time>
                            </span>
                        <span title="<?php echo $GLOBALS['t']['post']['author']; ?>">
                                <i class="icon-user" aria-hidden="true"></i>
                                <a href="<?php $this->author->permalink(); ?>" title="<?php echo $GLOBALS['t']['post']['author']; ?>: <?php $this->author(); ?>">
                                    <?php $this->author(); ?>
                                </a>
                            </span>
                        <span title="<?php echo $GLOBALS['t']['post']['views']; ?>">
                                <i class="icon-eye" aria-hidden="true"></i>
                                <?php echo postViews($this); ?>
                            </span>
                    </div>
                    <div class="post-content">
                        <?php if (count($linkArr)): ?>
                            <?php foreach ($linkArr as $link): ?>
                                <h2><?php echo $link['title']; ?></h2>
                                <div class="link-grid" aria-label="<?php echo $link['title']; ?>" role="group">
                                    <?php foreach ($link['links'] as $val): ?>
                                        <div class="link-item">
                                            <?php if (isset($val->logoUrl)): ?>
                                                <img class="logo" src="<?php echo $val->logoUrl; ?>" alt="<?php echo $val->name; ?>" loading="lazy">
                                            <?php else: ?>
                                                <div aria-label="<?php echo $val->name; ?>" role="img" class="logo-icon">
                                                    <i class="icon-link"></i>
                                                </div>
                                            <?php endif; ?>
                                            <a class="link-card" href="<?php echo $val->url; ?>" title="<?php echo isset($val->title)?$val->title:$val->name; ?>" target="_blank">
                                                <?php echo $val->name; ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <hr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php echo addExternalLinkAttributes(tableWrap($this->content), $this->options->siteUrl); ?>
                    </div>
                </article>
                <?php $this->need('components/comments.php'); ?>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</main>

<?php $this->need('components/footer.php'); ?>

<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'page';

// 语言初始化
languageInit();
$this->need('components/header.php');
?>

<main class="wrap" id="main">
    <div class="grid">
        <div class="post-page">
            <?php if ($this->options->breadcrumb == 'on'): ?>
                <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="crumbs">
                    <ol>
                        <li><a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a></li>
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
                    <?php if ($this->user->hasLogin()): ?>
                        <span>
                            <i class="icon-pencil" aria-hidden="true"></i>
                            <a href="<?php echo $this->options->adminUrl . 'write-page.php?cid=' . $this->cid; ?>"><?php echo $GLOBALS['t']['post']['edit']; ?></a>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="post-content">
                    <?php echo addExternalLinkAttributes(tableWrap($this->content), $this->options->siteUrl); ?>
                </div>
            </article>
            <?php $this->need('components/comments.php'); ?>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</main>
<?php $this->need('components/footer.php'); ?>

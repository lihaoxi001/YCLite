<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'post';
//  点赞请求
if (isset($_POST['agree'])) {
    if ($_POST['agree'] == $this->cid) {
        exit((string)agree($this->cid));
    }
    exit('error');
}

// 语言初始化
languageInit();
// 获取文章底部交互区域的按钮设置
$engagementSection = str_replace(' ', '', $this->options->engagementSection);
if ($engagementSection != '') $engagementSection = explode(',', $engagementSection);

$this->need('components/header.php');
?>

<main class="wrap" id="main">
    <div class="grid">
        <div class="post-page">
            <?php if ($this->options->breadcrumb == 'on'): ?>
                <nav aria-label="<?php echo $GLOBALS['t']['breadcrumb']; ?>" class="crumbs">
                    <ol>
                        <li><a href="<?php $this->options->siteUrl(); ?>"><?php echo $GLOBALS['t']['header']['home']; ?></a></li>
                        <li><?php $this->category(' '); ?></li>
                        <li tabindex="0" aria-current="page"><?php $this->title(); ?></li>
                    </ol>
                </nav>
            <?php endif; ?>
            <article class="post">
                <header>
                    <h1 class="post-title">
                        <a href="<?php $this->permalink(); ?>" rel="bookmark">
                            <?php
                            if ($this->hidden) {
                                echo $GLOBALS['t']['post']['thisPostIsPasswordProtected'];
                            }else {
                                $this->title();
                            }
                            ?>
                        </a>
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
                        <a rel="author" href="<?php $this->author->permalink(); ?>" title="作者：<?php $this->author(); ?>">
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
                          <a href="<?php echo $this->options->adminUrl . 'write-post.php?cid=' . $this->cid; ?>"><?php echo $GLOBALS['t']['post']['edit']; ?></a>
                      </span>
                    <?php endif; ?>
                </div>
                <div class="post-content">
                    <?php
                    // 使用分隔符给文章分页
                    $GLOBALS['postPage'] = splitArticleContent($this->content);
                    // 如果 url query 传入了页码就获取页码，否则默认为第一页
                    $postPageNum = isset($_GET['post-page'])?$_GET['post-page']:1;
                    // 如果通过 url 传入的页码找不到文章页面就把页码设置为第一页
                    if (!isset($GLOBALS['postPage'][$postPageNum - 1])) $postPageNum = 1;
                    // 生成章节目录
                    $GLOBALS['post'] = articleDirectory($GLOBALS['postPage'][$postPageNum - 1]);
                    // 生成表格样式
                    $GLOBALS['post']['content'] = tableWrap($GLOBALS['post']['content']);
                    // 站外链接新窗口打开
                    $GLOBALS['post']['content'] = addExternalLinkAttributes($GLOBALS['post']['content'], $this->options->siteUrl);
                    echo $GLOBALS['post']['content'];
                    ?>
                </div>

                <?php if (count($GLOBALS['postPage']) > 1): ?>
                    <nav aria-label="<?php echo $GLOBALS['t']['pagination']['postContentPagination']; ?>" class="post-pagination">
                        <div class="pagination">
                            <?php if ($postPageNum > 1): ?>
                                <a href="<?php echo $this->permalink . '?post-page=' . ($postPageNum - 1); ?>" class="prev-page" aria-label="<?php echo $GLOBALS['t']['pagination']['previousPage']; ?>">
                                    <i class="icon-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            <?php for ($i = 0;$i < count($GLOBALS['postPage']);$i ++): ?>
                                <?php if ($i == $postPageNum - 1): ?>
                                    <span class="active" aria-current="page"><?php echo $i + 1; ?></span>
                                <?php else: ?>
                                    <a href="<?php echo $this->permalink . '?post-page=' . ($i + 1); ?>"><?php echo $i + 1; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                            <?php if ($postPageNum < count($GLOBALS['postPage'])): ?>
                                <a href="<?php echo $this->permalink . '?post-page=' . ($postPageNum + 1); ?>" class="next-page" aria-label="<?php echo $GLOBALS['t']['pagination']['nextPage']; ?>">
                                    <i class="icon-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </nav>
                <?php endif; ?>

                <div class="post-tags clearfix">
                    <span class="float-left" role="group" aria-label="<?php echo $GLOBALS['t']['post']['category']; ?>">
                        <i class="icon-folder-open" aria-hidden="true"></i>
                        <?php ob_start(); $this->category(' '); $postCats = ob_get_clean(); echo trim($postCats) !== '' ? $postCats : $GLOBALS['t']['post']['uncategorized']; ?>
                    </span>
                    <span class="float-right" role="group" aria-label="<?php echo $GLOBALS['t']['post']['tag']; ?>">
                        <i class="icon-price-tag" aria-hidden="true"></i>
                        <?php postTags($this); ?>
                    </span>
                </div>
            </article>
            <?php if ($engagementSection != ''): ?>
                <div class="engage">
                    <?php foreach ($engagementSection as $val): ?>
                        <?php if ($val == '点赞'): ?>
                            <?php $agree = $this->hidden?array('agree' => 0, 'recording' => true):agreeNum($this->cid); ?>
                            <button type="button" class="btn agree-btn" <?php if ($agree['recording']) echo 'disabled'; ?> data-cid="<?php echo $this->cid; ?>" data-url="<?php $this->permalink(); ?>">
                                <i class="icon-thumbs-up"></i>
                                <span class="agree-num"><?php echo $GLOBALS['t']['post']['like']; ?> <?php echo $agree['agree']; ?></span>
                            </button>
                        <?php endif; ?>
                        <?php if ($val == '打赏'): ?>
                            <button type="button" class="btn btn-donate" data-collapse-target="#reward-qr" aria-expanded="false">
                                <i class="icon-coffee"></i>
                                <span><?php echo $GLOBALS['t']['post']['donate']; ?></span>
                            </button>
                        <?php endif; ?>
                        <?php if ($val == '分享'): ?>
                            <button type="button" class="btn btn-share" data-collapse-target="#qr-link" aria-expanded="false">
                                <i class="icon-share2"></i>
                                <span><?php echo $GLOBALS['t']['post']['share']; ?></span>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if (in_array('打赏', $engagementSection)): ?>
                        <div class="collapse-box" id="reward-qr" hidden>
                            <div class="qr">
                                <img src="<?php $this->options->rewardQr(); ?>" alt="<?php echo $GLOBALS['t']['post']['QRCode']; ?>">
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (in_array('分享', $engagementSection)): ?>
                        <div class="collapse-box" id="qr-link" hidden>
                            <p class="text-center"><?php echo $GLOBALS['t']['post']['scanTheQRCodeBelowToViewAndShareThisPageOnYourPhone']; ?></p>
                            <div class="text-center">
                                <canvas id="qr" aria-label="<?php echo $GLOBALS['t']['post']['QRCode']; ?>"></canvas>
                                <div class="link-box">
                                    <a href="https://service.weibo.com/share/share.php?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>" target="_blank" rel="external nofollow" aria-label="<?php echo $GLOBALS['t']['post']['shareOnWeibo']; ?>" title="<?php echo $GLOBALS['t']['post']['shareOnWeibo']; ?>">
                                        <i class="icon-sina-weibo"></i>
                                    </a>
                                    <a href="https://sns.qzone.qq.com/cgi-bin/qzshare/cgi_qzshare_onekey?url=<?php $this->permalink(); ?>&title=<?php $this->title(); ?>&site=<?php $this->options->siteUrl(); ?>&summary=<?php $this->fields->summaryContent?$this->fields->summaryContent():$this->excerpt($this->options->summary, '...'); ?>" target="_blank" rel="external nofollow" aria-label="<?php echo $GLOBALS['t']['post']['shareOnQzone']; ?>" title="<?php echo $GLOBALS['t']['post']['shareOnQzone']; ?>">
                                        <i class="icon-qzone-logo"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?php $this->permalink(); ?>&text=<?php $this->title(); ?>" target="_blank" rel="external nofollow" aria-label="<?php  echo $GLOBALS['t']['post']['shareOnTwitter']; ?>" title="<?php  echo $GLOBALS['t']['post']['shareOnTwitter']; ?>">
                                        <i class="icon-twitter"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <nav class="post post-nav" aria-label="post-navigation">
                <div class="previous">
                    <div><?php echo $GLOBALS['t']['post']['previousPost']; ?></div>
                    <div class="truncate"><?php $this->thePrev('%s', $GLOBALS['t']['post']['none']); ?></div>
                </div>
                <div class="next">
                    <div><?php echo $GLOBALS['t']['post']['nextPost']; ?></div>
                    <div class="truncate"><?php $this->theNext('%s', $GLOBALS['t']['post']['none']); ?></div>
                </div>
            </nav>
            <?php $this->need('components/comments.php'); ?>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
    <?php if ($this->options->directoryMobile == 'enable' && $GLOBALS['post']['directory'] != null): ?>
        <button type="button" id="directory-btn" class="fab" aria-label="<?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?>" title="<?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?>">
            <i class="icon-list-ol"></i>
        </button>
        <dialog id="toc-modal" class="toc-modal" aria-label="<?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?>">
            <div class="toc-head">
                <span><?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?></span>
                <form method="dialog"><button class="btn btn-sm" aria-label="<?php echo $GLOBALS['t']['sidebar']['closeTableOfContents']; ?>"><i class="icon-cancel-circle"></i></button></form>
            </div>
            <div class="toc-body">
                <?php echo $GLOBALS['post']['directory']; ?>
            </div>
        </dialog>
    <?php endif; ?>
</div>
</main>
<?php $this->need('components/footer.php'); ?>

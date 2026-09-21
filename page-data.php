<?php
/**
 * 网站数据
 *
 * @package custom
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

// 让主题使用的时区跟随 Typecho 设置的时区
setTimezoneByOffset($this->options->timezone);
// 语言初始化
languageInit();
// 获取分类数据
$categoryPostCount = categoryPostCount();
// 文章更新日历数据
$postCalendarData = postCalendar(time() - 20736000, time());
// 评论更新日历数据
$commentCalendarData = commentCalendar(time() - 20736000, time());

$GLOBALS['page'] = 'page-data';
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
                <div class="post-content">
                    <h2><?php echo $GLOBALS['t']['dataPage']['basicStatistics']; ?></h2>
                    <p class="muted"><?php echo $GLOBALS['t']['dataPage']['basicStatisticsDescription']; ?></p>
                    <?php Typecho_Widget::widget('Widget_Stat')->to($quantity); ?>
                    <div class="stat-grid">
                        <div class="stat-card">
                            <b><?php $quantity->publishedPostsNum(); ?></b>
                            <h4><?php echo $GLOBALS['t']['dataPage']['totalPosts']; ?></h4>
                        </div>
                        <div class="stat-card">
                            <b><?php $quantity->publishedCommentsNum(); ?></b>
                            <h4><?php echo $GLOBALS['t']['dataPage']['totalComments']; ?></h4>
                        </div>
                        <div class="stat-card">
                            <b><?php echo categoryCount(); ?></b>
                            <h4><?php echo $GLOBALS['t']['dataPage']['categories']; ?></h4>
                        </div>
                        <div class="stat-card">
                            <b><?php echo tagCount(); ?></b>
                            <h4><?php echo $GLOBALS['t']['dataPage']['tags']; ?></h4>
                        </div>
                        <div class="stat-card">
                            <b><?php echo viewsCount(); ?></b>
                            <h4><?php echo $GLOBALS['t']['dataPage']['totalViews']; ?></h4>
                        </div>
                        <div class="stat-card">
                            <b><?php echo agreeCount(); ?></b>
                            <h4><?php echo $GLOBALS['t']['dataPage']['totalLikes']; ?></h4>
                        </div>
                    </div>
                    <hr>
                    <h2><?php echo $GLOBALS['t']['dataPage']['categoryDistribution']; ?></h2>
                    <?php if (empty($categoryPostCount)): ?>
                        <p><?php echo $GLOBALS['t']['dataPage']['NoCategoryDataAvailableAtTheMoment']; ?></p>
                    <?php else: ?>
                        <p class="muted"><?php echo $GLOBALS['t']['dataPage']['categoryDistributionDescription']; ?></p>
                        <?php echo donutChart($categoryPostCount); ?>
                    <?php endif; ?>
                    <hr>
                    <h2><?php echo $GLOBALS['t']['dataPage']['postUpdates']; ?></h2>
                    <p class="muted"><?php printf($GLOBALS['t']['dataPage']['postUpdateDescription'], postDateFormat(time() - 20736000), postDateFormat(time())); ?></p>
                    <?php echo heatCalendar($postCalendarData); ?>
                    <hr>
                    <h2><?php echo $GLOBALS['t']['dataPage']['commentActivity']; ?></h2>
                    <p class="muted"><?php printf($GLOBALS['t']['dataPage']['commentActivityDescription'], postDateFormat(time() - 20736000), postDateFormat(time())); ?></p>
                    <?php echo heatCalendar($commentCalendarData); ?>
                    <hr>
                    <h2><?php echo $GLOBALS['t']['dataPage']['mostViewedPosts']; ?></h2>
                    <?php $top5Post = top5post(); ?>
                    <?php if (count($top5Post)): ?>
                        <p class="muted"><?php printf($GLOBALS['t']['dataPage']['mostViewedPostDescription'], count($top5Post)); ?></p>
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                <tr>
                                    <th><?php echo $GLOBALS['t']['dataPage']['rank']; ?></th>
                                    <th><?php echo $GLOBALS['t']['dataPage']['title']; ?></th>
                                    <th><?php echo $GLOBALS['t']['dataPage']['views']; ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $top = 1; ?>
                                <?php foreach ($top5Post as $post): ?>
                                    <tr>
                                        <td><?php echo $top; ?></td>
                                        <td><a href="<?php echo $post['link']; ?>"><?php echo $post['title']; ?></a></td>
                                        <td><?php echo $post['views']; ?></td>
                                    </tr>
                                    <?php $top ++; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p><?php echo $GLOBALS['t']['dataPage']['NoPostsAvailableAtTheMoment']; ?></p>
                    <?php endif; ?>
                    <hr>
                    <h2><?php echo $GLOBALS['t']['dataPage']['mostCommentedPosts']; ?></h2>
                    <?php $top5CommentPost = top5CommentPost(); ?>
                    <?php if (count($top5CommentPost)): ?>
                        <p class="muted"><?php printf($GLOBALS['t']['dataPage']['mostCommentedPostDescription'], count($top5CommentPost)); ?></p>
                        <div class="table-wrap">
                            <table class="data-table">
                                <thead>
                                <tr>
                                    <th><?php echo $GLOBALS['t']['dataPage']['rank']; ?></th>
                                    <th><?php echo $GLOBALS['t']['dataPage']['title']; ?></th>
                                    <th><?php echo $GLOBALS['t']['dataPage']['comments']; ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $top = 1; ?>
                                <?php foreach ($top5CommentPost as $post): ?>
                                    <tr>
                                        <td><?php echo $top; ?></td>
                                        <td><a href="<?php echo $post['link']; ?>"><?php echo $post['title']; ?></a></td>
                                        <td><?php echo $post['commentsNum']; ?></td>
                                    </tr>
                                    <?php $top ++; ?>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p><?php echo $GLOBALS['t']['dataPage']['NoPostsAvailableAtTheMoment']; ?></p>
                    <?php endif; ?>
                </div>
            </article>
            <?php $this->need('components/comments.php'); ?>
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</main>
<?php $this->need('components/footer.php'); ?>

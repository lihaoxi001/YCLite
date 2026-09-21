<?php
/**
 * 这是一套独立的博客主题，原生 CSS+JS 重构版（零依赖）
 *
 * @package YCLite
 * @author lihaoxi001
 * @version 1.0
 * @link https://github.com/lihaoxi001/FCLite
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = 'index';

// 语言初始化
languageInit();
// 检查数据库字段
checkField();
$this->need('components/header.php');
?>

<main class="wrap" id="main">
    <h1 class="sr-only"><?php $this->options->title(); ?></h1>
    <div class="grid">
        <div class="post-list">
            <?php if ($this->have()): ?>
            <?php $this->need('components/post-list.php'); ?>
            <?php if ($this->options->postPaginationType == 'loadMore'): ?>
                <nav hidden aria-label="<?php echo $GLOBALS['t']['pagination']['pagination']; ?>">
                    <?php $nextPageExists = paginate($this, $GLOBALS['t']['pagination']['previousPage'], $GLOBALS['t']['pagination']['nextPage']); ?>
                </nav>
                <?php if ($nextPageExists): ?>
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary load-more-btn"><?php echo $GLOBALS['t']['loadMore']['loadMore']; ?></button>
                    </div>
                <?php endif; ?>
            <?php else: ?>
            <nav aria-label="<?php echo $GLOBALS['t']['pagination']['pagination']; ?>">
                <?php paginate($this, $GLOBALS['t']['pagination']['previousPage'], $GLOBALS['t']['pagination']['nextPage']); ?>
            </nav>
            <?php endif; ?>
            <?php else: ?>
                <article class="no-content">
                    <h4 class="text-center mb-3" role="alert">没有可以显示的文章</h4>
                </article>
            <?php endif; ?>    
        </div>
        <?php $this->need('components/sidebar.php'); ?>
    </div>
</main>

<?php $this->need('components/footer.php'); ?>
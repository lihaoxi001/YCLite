<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$GLOBALS['page'] = '404';

// 语言初始化
languageInit();

$this->need('components/header.php');
?>

<main class="wrap page-404" id="main">
    <div class="page-404-notice" role="alert" aria-labelledby="page-title" aria-describedby="page-info">
        <h1 class="text-404" id="page-title">404</h1>
        <p id="page-info"><?php echo $GLOBALS['t']['page404']['thePageYouAreLookingForDoesNotExist']; ?></p>
    </div>
    <form class="search-form" action="<?php $this->options->siteUrl(); ?>" method="post" role="search" style="justify-content:center">
        <input type="search" placeholder="<?php echo $GLOBALS['t']['header']['search']; ?>" required name="s" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>">
        <button type="submit" aria-label="<?php echo $GLOBALS['t']['header']['search']; ?>"><i class="icon-search"></i></button>
    </form>
    <div class="text-center mt-4">
        <a href="<?php $this->options->siteUrl(); ?>" class="btn" id="back-home-page"><?php echo $GLOBALS['t']['page404']['goBackToHomepage']; ?></a>
    </div>
</main>

<?php $this->need('components/footer.php'); ?>

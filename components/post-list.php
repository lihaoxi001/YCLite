<?php

$postIndex = 0;
$listStyle = postListStyle($this->options->postListStyle, $this->fields->postListStyle);

// 公众号卡片样式
if ($listStyle == 'wechat'):
    $summaryLen = intval($this->options->summary) ?: 130;
    while ($this->next()):
    $postIndex++;
    $headerImage = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl);
?>
<div class="post-card-wechat<?php echo !$headerImage ? ' wechat-noimg' : ''; ?>">
    <a href="<?php $this->permalink(); ?>" class="card-link" aria-label="<?php $this->title(); ?>"></a>
    <?php if ($headerImage): ?>
    <div class="wechat-row">
        <div class="wechat-text">
            <h2 class="card-title"><?php $this->sticky(); ?><?php $this->title(); ?></h2>
            <p class="wechat-excerpt"><?php $this->excerpt($summaryLen); ?></p>
            <span class="wechat-date"><?php echo date('Y.n.j', $this->created); ?></span>
        </div>
        <div class="wechat-thumb">
            <img src="<?php echo $headerImage; ?>" alt="<?php $this->title(); ?>" width="160" height="120" <?php echo $postIndex > 1 ? 'loading="lazy"' : 'fetchpriority="high"'; ?> decoding="async">
        </div>
    </div>
    <?php else: ?>
    <div class="wechat-body">
        <h2 class="card-title"><?php $this->sticky(); ?><?php $this->title(); ?></h2>
        <p class="wechat-excerpt"><?php $this->excerpt($summaryLen); ?></p>
        <span class="wechat-date"><?php echo date('Y.n.j', $this->created); ?></span>
    </div>
    <?php endif; ?>
</div>
<?php
    endwhile;

// 默认卡片样式（原有）
else:
    while ($this->next()):
    $postIndex++;
    $headerImage = headerImageDisplay($this, $this->options->headerImage, $this->options->headerImageUrl);
?>
<div class="post-card<?php echo !$headerImage ? ' no-thumb' : ''; ?>">
    <div class="card-body">
        <a href="<?php $this->permalink(); ?>" class="card-link" aria-label="<?php $this->title(); ?>"></a>
        <h2 class="card-title">
            <?php $this->sticky(); ?>
            <?php
            if ($this->hidden) {
                echo $GLOBALS['t']['post']['thisPostIsPasswordProtected'];
            }else {
                $this->title();
            }
            ?>
        </h2>
            <div class="card-meta">
                <span><?php echo date('Y.n.j', $this->created); ?></span>
                <span><?php ob_start(); $this->category(','); $cardCats = ob_get_clean(); echo trim($cardCats) !== '' ? $cardCats : $GLOBALS['t']['post']['uncategorized']; ?></span>
                <span>💬 <?php $this->commentsNum('%d'); ?></span>
            </div>
        <div class="card-row">
            <?php if (!$this->hidden): ?>
            <p class="card-excerpt"><?php $this->excerpt(140); ?></p>
            <?php endif; ?>
            <?php if ($headerImage): ?>
            <div class="card-thumb">
                <img src="<?php echo $headerImage; ?>" alt="<?php $this->title(); ?>" width="180" height="120" <?php echo $postIndex > 1 ? 'loading="lazy"' : 'fetchpriority="high"'; ?> decoding="async">
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    endwhile;
endif;
?>

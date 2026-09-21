<?php if($this->allow('comment')): ?>

<div id="<?php $this->respondId(); ?>" class="comment-form">
    <h2><?php echo $GLOBALS['t']['comment']['leaveAComment']; ?></h2>
    <form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form">
        <?php $security = $this->widget('Widget_Security'); ?>
        <input type="hidden" name="_" value="<?php echo $security->getToken($this->request->getRequestUrl()); ?>">
        <label for="textarea">
            <?php echo $GLOBALS['t']['comment']['commentContent']; ?>
            <span class="required">*</span>
        </label>
        <textarea name="text" id="textarea" placeholder="<?php echo $GLOBALS['t']['comment']['enterYourCommentHere']; ?>" required></textarea>
        <!--Emoji表情区域-->
        <?php if ($this->options->emojiPanel == 'show'): ?>
        <div id="emoji-box">
            <button aria-expanded="false" type="button" class="btn btn-sm emoji-toggle" id="show-emoji-btn">
                😀 <?php echo $GLOBALS['t']['emoji']['emoji']; ?>
            </button>
            <div id="emoji-panel" class="emoji-panel" hidden role="dialog" aria-label="<?php echo $GLOBALS['t']['emoji']['emojiPanel']; ?>">
                <div id="emoji-classification" class="emoji-cats" role="group" aria-label="<?php echo $GLOBALS['t']['emoji']['emojiCategories']; ?>">
                    <button role="radio" aria-checked="true" aria-label="<?php echo $GLOBALS['t']['emoji']['smileys']; ?>" title="<?php echo $GLOBALS['t']['emoji']['smileys']; ?>" type="button" class="selected" data-classification="smileys">😀</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['peopleAndGestures']; ?>" title="<?php echo $GLOBALS['t']['emoji']['peopleAndGestures']; ?>" type="button" data-classification="character">👦</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['clothingAndAccessories']; ?>" title="<?php echo $GLOBALS['t']['emoji']['clothingAndAccessories']; ?>" type="button" data-classification="clothing">👕</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['animalsAndNature']; ?>" title="<?php echo $GLOBALS['t']['emoji']['animalsAndNature']; ?>" type="button" data-classification="animal">🐶</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['food']; ?>" title="<?php echo $GLOBALS['t']['emoji']['food']; ?>" type="button" data-classification="food">🍏</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['activity']; ?>" title="<?php echo $GLOBALS['t']['emoji']['activity']; ?>" type="button" data-classification="motion">⚽</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['travelAndPlaces']; ?>" title="<?php echo $GLOBALS['t']['emoji']['travelAndPlaces']; ?>" type="button" data-classification="tourism">🚚</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['objects']; ?>" title="<?php echo $GLOBALS['t']['emoji']['objects']; ?>" type="button" data-classification="objects">⌚</button>
                    <button role="radio" aria-checked="false" aria-label="<?php echo $GLOBALS['t']['emoji']['symbols']; ?>" title="<?php echo $GLOBALS['t']['emoji']['symbols']; ?>" type="button" data-classification="symbols">❤</button>
                </div>
                <h5 class="text-center" id="emoji-title">smileys</h5>
                <div id="emoji-list" class="emoji-list" role="list" aria-label="<?php echo $GLOBALS['t']['emoji']['emojiList'] . $GLOBALS['t']['emoji']['pressEnterToAddTheEmojiToTheCommentInputField']; ?>"></div>
            </div>
        </div>
        <?php endif; ?>
        <?php if($this->user->hasLogin()): ?>
            <p class="comment-user">
                <?php echo $GLOBALS['t']['comment']['loggedInAs']; ?>
                <a href="<?php $this->options->profileUrl(); ?>" title="当前登录身份：<?php $this->user->screenName(); ?>">
                    <?php $this->user->screenName(); ?>
                </a>.
                <a href="<?php $this->options->logoutUrl(); ?>" title="<?php echo $GLOBALS['t']['sidebar']['logout']; ?>"><?php echo $GLOBALS['t']['sidebar']['logout']; ?> &raquo;</a>
            </p>
        <?php else: ?>
            <div class="form-row cols-2">
                <div>
                    <label for="author">
                        <?php echo $GLOBALS['t']['comment']['name']; ?>
                        <span class="required">*</span>
                    </label>
                    <input type="text" placeholder="<?php echo $GLOBALS['t']['comment']['enterYourNameOrNickname']; ?>" name="author" id="author" value="<?php $this->remember('author'); ?>" required>
                </div>
                <div>
                    <label for="mail">
                        <?php echo $GLOBALS['t']['comment']['emailAddress']; ?>
                        <?php if ($this->options->commentsRequireMail): ?>
                            <span class="required">*</span>
                        <?php endif; ?>
                    </label>
                    <input type="email" placeholder="<?php echo $GLOBALS['t']['comment']['enterYourEmailAddress']; ?>" name="mail" id="mail" value="<?php $this->remember('mail'); ?>" <?php if ($this->options->commentsRequireMail) echo 'required'; ?>>
                </div>
            </div>
            <div>
                <label for="url">
                    <?php echo $GLOBALS['t']['comment']['website']; ?>
                    <?php if ($this->options->commentsRequireURL): ?>
                        <span class="required">*</span>
                    <?php endif; ?>
                </label>
                <input type="url" placeholder="<?php echo $GLOBALS['t']['comment']['enterYourWebsiteOrBlogURL']; ?>" name="url" id="url" value="<?php $this->remember('url'); ?>" <?php if ($this->options->commentsRequireURL) echo 'required'; ?>>
            </div>
        <?php endif; ?>
        <?php if (commentCaptchaEnabled()): ?>
            <!--评论图片验证码-->
            <div id="img-captcha">
                <img src="" alt="<?php echo $GLOBALS['t']['comment']['captchaImageAlt']; ?>" title="<?php echo $GLOBALS['t']['comment']['captchaImageAlt']; ?>" id="captcha-img" data-url="<?php $this->options->siteUrl(); ?>" width="160" height="50" fetchpriority="low" decoding="async">
            </div>
            <div>
                <label for="captcha-answer">
                    <?php echo $GLOBALS['t']['comment']['captchaImageLabel']; ?>
                    <span class="required">*</span>
                </label>
                <input type="number" name="captcha_answer" id="captcha-answer" placeholder="<?php echo $GLOBALS['t']['comment']['captchaImageLabel']; ?>" required>
                <input type="hidden" name="captcha_token" id="captcha-token" value="">
            </div>
        <?php endif; ?>
        <p class="mt-4">
            <button type="submit" class="btn btn-primary"><?php echo $GLOBALS['t']['comment']['submitComment']; ?></button>
            <?php $comments->cancelReply(); ?>
        </p>
    </form>
</div>

<?php else: ?>
    <div class="comment-off">
        <h2>评论功能已关闭</h2>
    </div>
<?php endif; ?>

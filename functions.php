<?php

require_once __DIR__ . '/inc/theme-config.php';
require_once __DIR__ . '/inc/theme-fields.php';
require_once __DIR__ . '/inc/helpers.php';
require_once __DIR__ . '/inc/captcha.php';

// 主题初始化钩子（Typecho 自动调用）：验证码 action 分发
function themeInit($archive) {
    // 输出评论图片验证码 JSON，输出后立即终止，避免附加页面内容
    if ((isset($_GET['action']) && $_GET['action'] == 'captcha') || (isset($_POST['action']) && $_POST['action'] == 'captcha')) {
        commentCaptchaImage();
        exit;
    }
}

// 取消脚本时间限制，防止 HyperDown 解析超时被 kill
@set_time_limit(0);
@ini_set('max_execution_time', '0');
@ini_set('memory_limit', '256M');
<?php

// 主题设置
// 分组说明：每个分组前用 fclite-group 标记输出分组名，后台 JS 按标记自动分组，
// 增删选项无需维护序号（旧的写死序号方案已废弃）。
function themeConfig($form) {
    // 后台保存/渲染设置时确保 views/agree 字段存在（服务端触发，无客户端）
    ensureColumns();
    echo <<<EOT
    <p>您现在使用的是 YCLite（原生 CSS + JS，零依赖）</p>
    <button aria-describedby="export-description" id="export-btn" type="button" class="btn">导出主题配置文件</button>
    <button aria-describedby="export-description" id="import-btn" type="button" class="btn">导入主题配置文件</button>
    <a href="javascript:;" id="download-file" style="display: none;">下载</a>
    <input type="file" id="file-select" style="display: none;">
    <p id="export-description"><b>导出主题配置文件</b> 可以把主题外观设置导出为 JSON 文件，主要用来备份主题设置，<b>导入主题配置文件</b> 可以导入 <b>YCLite</b> 主题的 JSON 配置文件。Typecho 切换主题的时候会清空主题设置，为了避免重复设置，在切换主题之前可以先导出主题设置配置。
    <div id="options-list">
        <h3>选项目录</h3>
        <ul aria-label="选项目录 - 点击可快速滚动到对应的选项分组"></ul>
        <button class="btn primary submit-options" type="button">保存设置</button>
    </div>
EOT;
    echo '<script type="text/javascript">';
    require_once __DIR__ . '/../assets/js/options-panel.js';
    echo '</script>';
    echo '<style type="text/css">';
    require_once __DIR__ . '/../assets/css/options-panel.css';
    echo '</style>';
    require_once __DIR__ . '/../components/link-editor.php';

    echo '<div class="fclite-group" data-title="外观" data-first="themeColor"></div>';
    // 主题配色
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('themeColor', array(
        'light-color' => '浅色主题',
        'dark-color' => '深色主题',
        'auto-color' => '跟随系统主题'
    ), 'auto-color', _t('默认主题配色'), _t('主题配色会优先使用访问者设置的配色，如果访问者没有更改过配色就会使用默认设置。配色切换按钮显示在顶部导航栏，侧边栏不再提供该组件。')));

    //  站点Logo
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('logoUrl', null, null, _t('站点 Logo icon 地址'), _t('Logo 是一个 ico 格式的 icon 图标，会显示在标签页的标题前面。')));

    //  站点副标题
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('tagline', null, '生命不息，折腾不止', _t('站点副标题'), _t('站点副标题会显示在标签页标题的后面。')));

    // 导航栏图片 logo
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('navLogoUrl', null, null, _t('站点 Logo 图片地址'), _t('站点 Logo 图片会显示在顶部导航栏的左侧，支持常见的图片格式，包括 SVG，只要能在 img 标签显示的图片都可以，留空会使用站点名称作为 Logo。')));

    // 深色模式导航栏图片 logo
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('navDarkLogoUrl', null, null, _t('深色模式 Logo 图片地址'), _t('如果您希望浅色模式和深色模式显示不同的 Logo，可以在这里填写深色模式的 Logo 图片 URL。留空则深浅模式都使用上方的站点 Logo 图片地址。')));

    // 站点 logo 图片高度限制
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('navLogoHeight', null, '30', _t('站点 Logo 图片高度限制'), _t('如果您发现导航栏 Logo 图片尺寸较小或过大的话，可以调整 Logo 图片的高度，可以直接填入数字，不需要加 px。')));

    // ICP 备案号
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('icp', null, null, _t('ICP备案号'), _t('ICP 备案号会显示在网站的底部，支持 a 标签。')));

    // 面包屑导航
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('breadcrumb', array(
        'on' => '开启',
        'off' => '关闭'
    ), 'off', _t('面包屑导航'), _t('开启后会在导航栏下方显示路径导航。')));

    echo '<div class="fclite-group" data-title="导航" data-first="navLinks"></div>';
    // 自定义导航栏链接
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('navLinks', null, null, _t('自定义导航栏链接'), _t('您可以在导航栏添加自定义链接，链接的名称和 URL 都可以自定义，导航栏链接需要使用 JSON 配置 <a href="https://facile.misterma.com/%E4%B8%BB%E9%A2%98%E8%AE%BE%E7%BD%AE.html#%E8%BF%9B%E5%85%A5%E4%B8%BB%E9%A2%98%E8%AE%BE%E7%BD%AE" target="_blank">点击查看配置说明</a>。')));

    echo '<div class="fclite-group" data-title="侧边栏" data-first="sidebarComponent"></div>';
    //  侧边栏组件顺序
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('sidebarComponent', null, '博客信息，最新文章，最新回复，文章分类，标签云，文章归档，其它功能，友情链接，自定义', _t('侧边栏组件'), _t('您可以设置需要显示在侧边栏的组件，组件会根据这里的组件名称排序。组件名称之间用英文逗号分隔，逗号和名称之间不需要空格，结尾不需要逗号。例如 <b style="color: #C7254E;">博客信息，最新文章，最新回复，文章分类，标签云，文章归档，其它功能，友情链接，自定义</b> 。自定义组件主要用于显示自定义 HTML，开启后需要在下方的 <b style="color: #C7254E;">侧边栏自定义 HTML 内容</b> 表单填写内容后才会显示。')));

    // 文章页的侧边栏组件顺序
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('postPageSidebarComponent', null, '博客信息，最新文章，目录', _t('文章页的侧边栏组件'), _t('这里可以单独设置文章页的侧边栏组件，组件会根据这里的组件名称排序。组件名称之间用英文逗号分隔，逗号和名称之间不需要空格，结尾不需要逗号。例如 <b style="color: #C7254E;">博客信息，最新文章，目录</b> 。其中目录组件只能在文章页显示，目录列表项会根据文章内插入的标题生成，如果文章内没有插入标题就不会显示目录，目录组件滚动到页面上方时位置会被固定，建议把目录放到最后。')));

    // 侧边栏最新文章数量
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('postsListSize', null, '5', _t('侧边栏最新文章数量'), _t('侧边栏最新文章组件显示的文章数量。')));

    //  侧边栏博客信息博主头像地址
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('avatarUrl', null, null, _t('博主头像地址'), _t('博主头像会显示在侧边栏的博客信息区域，如果省略会使用管理员的 Gravatar 头像。')));

    //  侧边栏博客信息区域博主昵称
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('nickname', null, null, _t('博主昵称'), _t('博主昵称会显示在侧边栏博客信息区域，如果省略会显示管理员昵称。')));

    //  侧边栏博客信息博主昵称链接
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('nicknameUrl', null, null, _t('博主昵称链接跳转地址'), _t('在侧边栏的博客信息区域会显示一个包含博主昵称的链接，在这里可以填写链接的跳转地址，如果省略会使用博客首页地址。')));

    //  侧边栏博客信息博主简介
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('Introduction', null, null, _t('博主简介'), _t('博主简介会显示在侧边栏博客信息区域的博主昵称下方，如果省略会使用设置中的站点描述信息。')));

    //  侧边栏博客信息的运行天数
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('birthday', null, null, _t('站点创建时间'), _t('在这里填写站点创建时间后，在侧边栏的博客信息区域就会显示网站运行天数。如果省略 网站运行天数会从管理员账号创建的时间开始计算天数。站点创建时间的格式为：yyyy-mm-dd，例如：2019-11-11。')));

    //  侧边栏标签数量
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('tagCount', null, '0', _t('侧边栏标签云标签数量'), _t('对于标签较多的博客，可以设置侧边栏显示的标签数量，0 为不限制。')));

    // 侧边栏自定义HTML标题
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('customizeTitle', null, '公告', _t('侧边栏自定义 HTML 组件标题'), _t('如果您启用了侧边栏的自定义 HTML 组件，可以在这里给组件设置一个标题，这个标题会显示在组件上方。')));

    // 侧边栏自定义HTML
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('customizeHTML', null, null, _t('侧边栏自定义 HTML 内容'), _t('如果您启用了侧边栏的自定义 HTML 组件，可以在这里输入 HTML，支持纯文本和 HTML，包括 img、audio、video、canvas。您可以用来设置网站公告内容或广告。')));

    //  登录入口
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('loginLink', array(
        'show' => '显示',
        'hide' => '隐藏'
    ), 'show', _t('登录入口'), _t('隐藏登录入口后在前台就不会显示登录入口，只能通过 域名/admin/login.php 进入登录页面')));

    echo '<div class="fclite-group" data-title="文章列表" data-first="postListStyle"></div>';
    // 文章列表显示设置
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('postListStyle', array(
        'summary' => '图文卡片（标题+摘要+缩略图）',
        'wechat' => '微信风（标题+摘要+日期，右缩略图）'
    ), 'summary', _t('文章列表显示'), _t('图文卡片为标题+摘要+缩略图三段式；微信风为左文右图，包含标题、摘要和日期。')));

    //  文章摘要字数
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('summary', null, '130', _t('文章摘要字数'), _t('微信风列表样式的文章摘要字数，默认为 130 个字。图文卡片样式的摘要固定为 140 字，不受此设置影响。')));

    // 文章列表分页方式
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('postPaginationType', array(
        'links' => '数字分页链接',
        'loadMore' => '点击加载更多'
    ), 'links', _t('文章列表分页方式'), _t('设置首页和其它归档页的文章列表分页方式。点击加载更多通过 JS 追加文章，无 JS 时自动回退数字分页。')));

    //  文章头图设置
    $headerImage = new Typecho_Widget_Helper_Form_Element_Checkbox('headerImage', array(
        'home' => _t('在文章列表显示文章头图'),
        'post' => _t('在文章页显示文章头图')
    ), array('home', 'post'), _t('文章头图显示设置'), _t('这里可以统一设置文章头图的显示和隐藏，您也可以在文章编辑页给文章单独设置显示和隐藏。'));
    $form->addInput($headerImage->multiMode());

    //  默认文章头图
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('headerImageUrl', null, null, _t('默认文章头图'), _t('这里可以填写默认的文章头图 URL，一行一个，系统会在默认文章头图地址中随机选择一个来加载文章头图。要使用默认文章头图，文章编辑页的文章头图来源需要设置为 使用系统设置。')));

    // WebP 缩略图
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('webpThumbnail', array(
        'on' => '启用',
        'off' => '禁用'
    ), 'off', _t('WebP 缩略图'), _t('开启后文章列表页的文章头图会自动生成 3:2 裁剪的 WebP 缩略图，大幅减小图片体积（通常减少 90%+），提升页面加载速度。<br><br><b style="color:#C7254E;">使用前请确保满足以下条件：</b><br><b>① PHP 扩展</b>：服务器 PHP 需安装 <code>GD</code> 扩展且启用 <code>imagewebp</code> 函数（PHP 5.4+ 自带 GD，但部分环境可能未编译 WebP 支持）。可在 PHP 信息页面搜索 <code>WebP</code> 确认。<br><b>② 缓存目录权限</b>：PHP 进程需要对主题目录下的 <code>assets/cache/thumbs/</code> 有<strong>写权限</strong>。如果使用 Docker 部署，需确保宿主机目录权限映射正确（容器内 www-data 用户的 UID 需有写权限）。<br><b>③ 原图格式</b>：支持 JPG、PNG、GIF 格式的原图，WebP 格式原图会直接跳过不处理。<br><br>启用后首次访问列表页时会逐个生成缩略图，之后访问将命中缓存。缩略图缓存在 <code>assets/cache/thumbs/</code> 目录下，删除该目录即可清空缓存。')));

    echo '<div class="fclite-group" data-title="文章页" data-first="directoryMobile"></div>';
    // 移动设备章节目录
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('directoryMobile', array(
        'enable' => '启用',
        'disabled' => '禁用'
    ), 'enable', _t('移动设备章节目录'), _t('开启后在没有侧边栏的小屏移动设备右下方会显示一个目录按钮，点击可以打开章节目录列表。')));

    // 文章底部的交互功能配置
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('engagementSection', null, '点赞,分享', _t('文章底部的交互功能'), _t('文章底部要使用的交互功能，支持 <b style="color: #C7254E;">点赞,打赏,分享</b>，功能名称之间用英文逗号分隔，逗号之间不需要空格，结尾不需要逗号，功能按钮的顺序会根据这里设置的名称顺序排序。')));

    // 打赏二维码地址
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('rewardQr', null, '', _t('打赏二维码图片地址'), _t('文章下方的打赏按钮点击后可以显示一个二维码图片，你可以在这里设置图片地址，图片的最大宽度就是文章区域的宽度，高度不限制，图片会居中显示。')));

    echo '<div class="fclite-group" data-title="代码高亮" data-first="codeHighlight"></div>';
    // 启用代码高亮功能
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('codeHighlight', array(
        'enable-highlight' => '启用',
        'disabled-highlight' => '禁用'
    ), 'enable-highlight', _t('代码高亮'), _t('您可以设置是否启用文章内的代码块高亮，如果您需要使用其他代码高亮插件的话，可以禁用主题自带的代码高亮功能。')));

    // 代码块配色
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('codeThemeColor', array(
        'stackoverflow-light' => 'Stack Overflow（浅色）',
        'vs2015' => 'VS2015（深色）',
        'sunburst' => 'Sunburst（高对比度）',
        'follow-theme-color' => '跟随主题配色',
        'custom-code-theme' => '自定义'
    ), 'vs2015', _t('代码块颜色主题'), _t('跟随主题配色会根据主题使用的配色模式来自动选择代码块的颜色主题，如果主题为浅色模式，使用 Stack Overflow，如果主题为深色模式，使用 VS2015。')));

    // 自定义代码高亮 CSS
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('highlightJsCSS', null, null, _t('自定义代码块颜色主题'), _t('如果代码块颜色主题选择了自定义，可以在这里输入 highlight.js 的配色 CSS URL，也可以直接粘贴 highlight.js 的 CSS 代码。配色可到 cdnjs 的 highlight.js 页面获取，到 highlight.js 官网可预览效果。使用自定义配色建议关闭代码块行号，行号只适配内置主题。')));

    echo '<div class="fclite-group" data-title="评论" data-first="commentDateFormat"></div>';
    //  评论日期时间格式
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('commentDateFormat', array(
        'format1' => '2020年04月23日 13:09',
        'format2' => '2020-04-23 13:09',
        'format3' => 'April 23rd, 2020 at 01:09 pm',
        'format4' => '时间间隔（3天前）'
    ), 'format1', _t('评论日期时间格式'), _t('时间间隔的单位会根据间隔长短变化，不到一分钟的单位为 秒，一分钟以上、一小时以下的单位为 分钟，一小时以上、一天以下的单位为 小时，一天以上的单位为 天，')));

    //  评论框位置
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('commentInput', array(
        'top' => '评论表单在评论列表上方',
        'bottom' => '评论表单在评论列表下方'
    ), 'bottom', _t('评论表单位置'), _t('评论表单就是发表评论的区域，评论列表就是已发表的评论区域')));

    //  使用QQ头像
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('QQAvatar', array(
        'show' => '显示',
        'hide' => '不显示'
    ), 'hide', _t('显示评论者的QQ头像'), _t('开启后如果检测到评论者使用QQ邮箱就会显示QQ头像，只支持 QQ号@qq.com 的QQ邮箱。')));

    // 自定义 Gravatar 地址
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('gravatarUrl', null, '', _t('自定义 Gravatar 源'), _t('Gravatar 头像服务在有些地区可能无法正常使用，如果你需要更换 Gravatar 源的话，可以在这里输入 URL，留空会使用官方源。')));

    //  启用 Emoji 面板
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('emojiPanel', array(
        'show' => '启用',
        'hide' => '禁用'
    ), 'show', _t('Emoji 表情面板'), _t('开启后在评论内容输入框下方会出现一个 Emoji  表情按钮，点击可以打开表情面板。')));

    //  评论验证码
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('commentCaptcha', array(
        'image' => '图片算数验证码',
        'disable' => '关闭'
    ), 'disable', _t('评论验证码'), _t('开启后评论框会显示一道简单的加法题（例如 3 + 5 = ？），访客算出结果填进去才能提交，不需要依赖第三方服务，但只能挡住简单的机器人。')));

    //  评论验证码密钥
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Text('commentCaptchaSecret', null, '12345678', _t('评论验证码密钥'), _t('密钥会参与验证码的防伪运算，建议填写一串不含空格的随机字母和数字，修改后刷新评论页面即可生效。留空默认使用 12345678。')));

    echo '<div class="fclite-group" data-title="SEO" data-first="searchPageNoindex"></div>';
    // 搜索页添加 noindex
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('searchPageNoindex', array(
        'show' => '启用',
        'hide' => '禁用'
    ), 'hide', _t('搜索结果页添加 noindex 标签'), _t('开启后会在搜索结果页的 head 区域添加 noindex，告诉搜索引擎不要收录搜索结果页。这可以有效避免网站因被垃圾广告机器人频繁搜索而在 Google 等搜索结果中出现大量无效广告页面。')));

    // 归档页添加 noindex
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('dateArchivePageNoindex', array(
        'show' => '启用',
        'hide' => '禁用'
    ), 'hide', _t('日期归档页添加 noindex 标签'), _t('开启后会在日期归档页的 head 区域添加 noindex，告诉搜索引擎不要收录日期归档页。侧边栏的文章归档组件会按月份生成文章归档链接和页面，如果你的网站建站较早的话，可能会生成大量归档页面，在有分类和标签归档页的情况下，这些日期归档页对于搜索引擎来说属于重复页面。大量的日期归档页可能会影响到文章页面的权重，而且用户一般也不会在搜索引擎搜索文章归档页。')));

    // 作者归档页添加 noindex
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Radio('authorArchivePageNoindex', array(
        'show' => '启用',
        'hide' => '禁用'
    ), 'show', _t('作者归档页添加 noindex 标签'), _t('开启后会在作者归档页的 head 区域添加 noindex。个人博客通常只有一个作者，作者归档页与首页内容高度重复，不建议被搜索引擎收录。')));

    echo '<div class="fclite-group" data-title="友情链接" data-first="homeLinks"></div>';
    //  首页友链
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('homeLinks', null, null, _t('首页友情链接'), _t('首页友情链接只会显示在首页的侧边栏，需要 JSON 格式数据。 <a href="https://facile.misterma.com/%E4%B8%BB%E9%A2%98%E8%AE%BE%E7%BD%AE.html" target="_blank">点击查看友情链接设置说明</a>，你也可以使用链接编辑器编辑，无需手动输入 JSON。。 <button data-title="首页友情链接" data-name="homeLinks" type="button" class="btn show-link-editor">打开链接编辑器</button>')));

    //  全站友链
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('links', null, null, _t('全站友情链接'), _t('全站友情链接会在每个页面的侧边栏显示，需要 JSON 格式数据。<a href="https://facile.misterma.com/%E4%B8%BB%E9%A2%98%E8%AE%BE%E7%BD%AE.html" target="_blank">点击查看友情链接设置说明</a>，你也可以使用链接编辑器编辑，无需手动输入 JSON。 <button data-title="全站友情链接" data-name="links" type="button" class="btn show-link-editor">打开链接编辑器</button>')));

    //  独立页友链
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('pageLinks', null, null, _t('独立页友情链接'), _t('独立页友情链接只会在友情链接的页面显示，需要 JSON 格式 数据。如果要使用独立页友情链接需要创建一个独立页面，把 自定义模板设置为 <b style="color: #C7254E;">友情链接</b>。<a href="https://facile.misterma.com/%E4%B8%BB%E9%A2%98%E8%AE%BE%E7%BD%AE.html" target="_blank">点击查看友情链接设置说明</a>，你也可以使用链接编辑器编辑，无需手动输入 JSON。 <button data-title="独立页友情链接" data-name="pageLinks" type="button" class="btn show-link-editor">打开链接编辑器</button>')));

    // 在链接页面显示首页和全站链接
    $linkPageOptions = new Typecho_Widget_Helper_Form_Element_Checkbox('linkPageOptions', array(
        'showSitewideOnLinkPage' => _t('同时在链接页面展示全站链接'),
        'showHomepageOnLinkPage' => _t('同时在链接页面展示首页链接')
    ), array('showSitewideOnLinkPage', 'showHomepageOnLinkPage'), _t('友情链接页面展示设置'), _t('友情链接页面除了能展示内页链接外，也能展示首页和全站链接。链接页面效果可以查看 <a href="https://facile.misterma.com/%E4%B8%BB%E9%A2%98%E8%AE%BE%E7%BD%AE.html" target="_blank">Facile主题帮助文档 - 主题设置</a>，也可以查看我的博客的 <a href="https://www.misterma.com/links.html" target="_blank">友情链接页面</a>。'));
    $form->addInput($linkPageOptions->multiMode());

    echo '<div class="fclite-group" data-title="自定义代码" data-first="cssCode"></div>';
    //  自定义CSS
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('cssCode', null, null, _t('自定义 CSS'), _t('通过自定义 CSS 您可以很方便的设置页面样式，自定义 CSS 不会影响网站源代码。')));

    //  自定义 head 输出的 HTML
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('headHTML', null, null, _t('自定义 head 区域输出的 HTML'), _t('head 区域的 HTML 会在 head 内输出，可以用来定义一些网站统计的 JS 之类的。')));

    //  自定义 body 底部的 HTML
    $form->addInput(new Typecho_Widget_Helper_Form_Element_Textarea('bodyHTML', null, null, _t('自定义 body 底部输出的 HTML'), _t('body 底部的 HTML 会在 footer 之后 body 尾部之前输出。')));
}

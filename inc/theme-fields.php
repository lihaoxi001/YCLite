<?php

// 文章的自定义字段
function themeFields($layout) {
    // 文章列表显示设置
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('postListStyle', array(
        'default' => '使用系统设置',
        'summary' => '不显示文章摘要（左图+标题+日期）',
        'wechat' => '显示文章摘要（左图+标题+摘要+日期）'
    ), 'default', _t('文章列表显示'), _t('单独设置此文章的列表显示样式。')));

    // 文章头图显示设置
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('headerImgDisplay', array(
        'default' => '使用系统设置',
        'post-page-list' => '在文章列表和文章页显示文章头图',
        'post-list' => '只在文章列表显示文章头图',
        'post-page' => '只在文章页显示文章头图',
        'hide' => '不显示文章头图'
    ), 'default', _t('文章头图显示设置'), _t('您可以单独给文章设置文章头图显示。')));

    // 文章头图样式（固定为小图）
    // 小图（文章标题和摘要在左侧，图片在右侧）

    //  文章头图来源
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Select('imageSource', array(
        'article' => '使用文章中的第一张图片作为文章头图',
        'url' => '在文章头图输入框手动输入图片URL',
        'default' => '使用系统设置'
    ), 'article', _t('文章头图来源'), _t('如果选择了使用文章中的第一张图片作为文章头图，在文章不包含图片的情况下将不会显示文章头图。如果选择了使用系统设置，需要在主题设置的默认文章头图输入框填写图片 URL，系统会在默认文章头图中随机选择一个 URL 加载。')));

    //  文章头图
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('thumb', null, null, _t('文章头图'), _t('如果您在文章头图来源中设置了手动输入图片 URL 的话，请在这里输入图片 URL。')));

    //  自定义文章摘要内容
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Textarea('summaryContent', null, null, _t('自定义摘要内容'), _t('您可以在此处为文章定义摘要内容，此处定义的摘要内容不受字数限制。')));

    //  自定义关键词
    $layout->addItem(new Typecho_Widget_Helper_Form_Element_Text('keywords', null, null, _t('自定义关键词'), _t('您可以输入这篇文章的关键词，多个关键词之间用英文逗号分隔，如果为空 会使用这篇文章的标签作为关键词。')));
}
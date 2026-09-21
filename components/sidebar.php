<?php
// 读取侧边栏组件
$components = $GLOBALS['page'] == 'post'?$this->options->postPageSidebarComponent:$this->options->sidebarComponent;
// 如果侧边栏组件为空就使用默认设置
if ($components == null or $components == '') {
    $components = '搜索,最新文章,最新回复,文章分类,标签云,文章归档,其它功能,友情链接';
}
// 去除空格
$components = str_replace(' ', '', $components);
$components = str_replace('，', ',', $components);
// 转为数组
$components = explode(',', $components);
?>

<aside class="sidebar">
    <?php foreach ($components as $component): ?>
        <?php if ($component == '博客信息'): ?>
            <!--博客信息-->
            <section class="  blog-info">
                <h2><?php echo $GLOBALS['t']['sidebar']['blogInfo']; ?></h2>
                <div>
                    <?php if (!$this->options->nickname or !$this->options->birthday or !$this->options->avatarUrl) $userInfo = getAdminInfo(); ?>
                    <div class="blog-user">
                        <?php
                            $avatarName = $this->options->nickname?$this->options->nickname . '的头像':$this->options->title . '的头像';
                            if ($this->options->avatarUrl) {
                                echo '<img src="' . $this->options->avatarUrl . '" alt="' . $avatarName . '" class="avatar" width="56" height="56" />';
                            }else {
                                gravatar($userInfo['mail'], 56, $this->options->gravatarUrl, $avatarName);
                            }
                        ?>
                        <div>
                            <h3><a aria-describedby="blog-description" href="<?php echo $this->options->nicknameUrl?$this->options->nicknameUrl:$this->options->siteUrl; ?>" target="_blank"><?php echo $this->options->nickname?$this->options->nickname:$userInfo['screenName']; ?></a></h3>
                            <p id="blog-description"><?php echo $this->options->Introduction?$this->options->Introduction:$this->options->description; ?></p>
                        </div>
                    </div>
                    <hr>
                    <div class="stats">
                        <?php Typecho_Widget::widget('Widget_Stat')->to($quantity); ?>
                        <div>
                            <p><i class="icon-award"></i> <?php printf($GLOBALS['t']['sidebar']['totalPosts'], $quantity->publishedPostsNum); ?></p>
                        </div>
                        <div>
                            <p><i class="icon-bubble"></i> <?php printf($GLOBALS['t']['sidebar']['totalComments'], $quantity->publishedCommentsNum); ?></p>
                        </div>
                        <div>
                            <p><i class="icon-eye"></i> <?php printf($GLOBALS['t']['sidebar']['totalViews'], viewsCount()); ?></p>
                        </div>
                        <div>
                            <?php $runningSince = $this->options->birthday ? round((time() - strtotime($this->options->birthday)) / 86400, 0) : round((time() - $userInfo['created']) / 86400, 0); ?>
                            <p><i class="icon-calendar"></i> <?php printf($GLOBALS['t']['sidebar']['runningSince'], $runningSince); ?></p>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
        <?php if ($component == '自定义' && $this->options->customizeHTML): ?>
            <!--自定义HTML-->
            <section class="  customize">
                <h2><?php $this->options->customizeTitle(); ?></h2>
                <div class="customize-html"><?php $this->options->customizeHTML(); ?></div>
            </section>
        <?php endif; ?>
        <?php if ($component == '最新文章'): ?>
            <!--最新文章-->
            <section class=" ">
                <h2><?php echo $GLOBALS['t']['sidebar']['latestPosts']; ?></h2>
                <?php $latestArticles = $this->widget('Widget_Contents_Post_Recent'); ?>
                <?php $postSize = 0; ?>
                <?php if ($latestArticles->have()): ?>
                    <ul aria-label="<?php echo $GLOBALS['t']['sidebar']['latestPosts']; ?>">
                        <?php while ($latestArticles->next()): ?>
                            <li>
                                <a href="<?php $latestArticles->permalink(); ?>"><?php $latestArticles->title(); ?></a>
                            </li>
                            <?php
                            $postSize ++;
                            $postsListSize = intval($this->options->postsListSize) ?: 5;
                            if ($postSize >= $postsListSize) {
                                break;
                            }
                            ?>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="muted"><?php echo $GLOBALS['t']['sidebar']['noPostsAvailableToDisplay']; ?></p>
                <?php endif; ?>    
            </section>
        <?php endif; ?>
        <?php if ($component == '最新回复'): ?>
            <!--最新回复-->
            <section class="latest-comment">
                <h2><?php echo $GLOBALS['t']['sidebar']['recentComments']; ?></h2>
                <ul aria-label="<?php echo $GLOBALS['t']['sidebar']['recentComments']; ?>" class="list-unstyled">
                    <?php $this->widget('Widget_Comments_Recent')->to($comments); ?>
                    <?php if ($comments->have()): ?>
                        <?php while($comments->next()): ?>
                            <li class="comment-mini">
                                <?php
                                    // 普通评论头像
                                    if ($comments->type == 'comment') {
                                        if ($this->options->QQAvatar == 'show' && isQQEmail($comments->mail)) {
                                            QQAvatar($comments->mail, $comments->author, 40);
                                        }else {
                                            gravatar($comments->mail, 50, $this->options->gravatarUrl, $comments->author);
                                        }
                                    }
                                    // 引用头像
                                    if ($comments->type == 'pingback') {
                                        echo '<div class="pingback avatar" role="img" aria-label="引用">引用</div>';
                                    }
                                ?>
                                <div class="comment-mini-body">
                                    <h3>
                                        <a href="<?php $comments->permalink(); ?>" title="<?php printf($GLOBALS['t']['sidebar']['commentOn'], $comments->title); ?>">
                                            <?php $comments->author(false); ?>
                                        </a>
                                    </h3>
                                    <p><?php $comments->excerpt(40, '...'); ?></p>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>    
                        <p class="muted"><?php echo $GLOBALS['t']['sidebar']['noCommentsOrRepliesAvailableToDisplay']; ?></p>
                    <?php endif; ?>    
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '文章分类'): ?>
            <!--分类-->
            <section class="  category">
                <h2><?php echo $GLOBALS['t']['sidebar']['categories']; ?></h2>
                <ul aria-label="<?php echo $GLOBALS['t']['sidebar']['categories']; ?>">
                    <?php $this->widget('Widget_Metas_Category_List')->to($category); ?>
                    <?php if ($category->have()): ?>
                        <?php while ($category->next()): ?>
                            <li <?php if ($category->parent > 0) echo 'class="ml-3"'; ?>>
                                <a rel="index" title="<?php if ($category->parent > 0) echo getParentCategory($category->parent) . ' 下的子分类 ' ?><?php $category->description(); ?>" href="<?php $category->permalink(); ?>">
                                    <?php $category->name(); ?>
                                    (<?php $category->count(); ?>)
                                </a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="muted"><?php echo $GLOBALS['t']['sidebar']['noCategoriesAvailableToDisplay']; ?></p>
                    <?php endif; ?>    
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '标签云'): ?>
            <!--标签云（文字云：字号按文章数分 5 档，颜色 5 色轮换）-->
            <section class="tags">
                <h2><?php echo $GLOBALS['t']['sidebar']['tags']; ?></h2>
                <?php $limit = $this->options->tagCount == 0?1000:$this->options->tagCount; ?>
                <?php $this->widget('Widget_Metas_Tag_Cloud', 'sort=mid&ignoreZeroCount=1&desc=0&limit=' . $limit)->to($tags); ?>
                <?php if($tags->have()): ?>
                    <?php
                    $cloud = array();
                    $cloudMin = null;
                    $cloudMax = 0;
                    while ($tags->next()):
                        ob_start();
                        $tags->name();
                        $cloudName = ob_get_clean();
                        ob_start();
                        $tags->permalink();
                        $cloudLink = ob_get_clean();
                        $cloudCount = intval($tags->count);
                        $cloud[] = array(
                            'name' => $cloudName,
                            'link' => $cloudLink,
                            'count' => $cloudCount,
                            'title' => sprintf($GLOBALS['t']['sidebar']['tagPostCount'], $tags->count)
                        );
                        if ($cloudMin === null || $cloudCount < $cloudMin) $cloudMin = $cloudCount;
                        if ($cloudCount > $cloudMax) $cloudMax = $cloudCount;
                    endwhile;
                    $cloudSpan = max(1, $cloudMax - $cloudMin);
                    $cloudPalette = array('c1', 'c2', 'c3', 'c4', 'c5');
                    ?>
                    <div class="tagcloud" role="list" aria-label="<?php echo $GLOBALS['t']['sidebar']['tags']; ?>">
                        <?php foreach ($cloud as $i => $t): ?>
                            <?php $cloudLv = intval(1 + 4 * ($t['count'] - $cloudMin) / $cloudSpan); ?>
                            <a role="listitem" href="<?php echo $t['link']; ?>" rel="tag" title="<?php echo htmlspecialchars($t['title']); ?>" class="tw lv<?php echo $cloudLv; ?> <?php echo $cloudPalette[$i % 5]; ?>"><?php echo $t['name']; ?><sup><?php echo $t['count']; ?></sup></a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="muted"><?php echo $GLOBALS['t']['sidebar']['noTagsAvailableToDisplay']; ?></p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
        <?php if ($component == '文章归档'): ?>
            <!--归档-->
            <section class="  archive">
                <h2><?php echo $GLOBALS['t']['sidebar']['archives']; ?></h2>
                <?php
                // 归档时间格式
                $format = 'Y年m月';
                $postArchive = $this->widget('Widget_Contents_Post_Date', 'type=month&format=' . $format);
                ?>
                <?php if ($postArchive->have()): ?>
                    <ul aria-label="<?php echo $GLOBALS['t']['sidebar']['archives']; ?>">
                        <?php while ($postArchive->next()): ?>
                            <li>
                                <a rel="archives" href="<?php $postArchive->permalink(); ?>" class="mr-2">
                                    <?php $postArchive->date(); ?>
                                    (<?php $postArchive->count(); ?>)
                                </a>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="muted"><?php echo $GLOBALS['t']['sidebar']['coPostsAvailableToGenerateAnArchive']; ?></p>
                <?php endif; ?>    
            </section>
        <?php endif; ?>
        <?php if ($component == '其它功能'): ?>
            <!--其它功能-->
            <section class=" ">
                <h2><?php echo $GLOBALS['t']['sidebar']['other']; ?></h2>
                <ul aria-label="<?php echo $GLOBALS['t']['sidebar']['other']; ?>">
                    <?php if ($this->options->loginLink == 'show'): ?>
                        <?php if($this->user->hasLogin()): ?>
                            <li>
                                <a href="<?php $this->options->adminUrl(); ?>"><?php printf($GLOBALS['t']['sidebar']['dashboard'], $this->user->screenName); ?></a>
                            </li>
                            <li>
                                <a href="<?php $this->options->logoutUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['logout']; ?></a>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?php $this->options->adminUrl('login.php'); ?>"><?php echo $GLOBALS['t']['sidebar']['login']; ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                    <li>
                        <a href="<?php $this->options->feedUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['RSSforPosts']; ?></a>
                    </li>
                    <li>
                        <a href="<?php $this->options->commentsFeedUrl(); ?>"><?php echo $GLOBALS['t']['sidebar']['RSSforComments']; ?></a>
                    </li>
                </ul>
            </section>
        <?php endif; ?>
        <?php if ($component == '友情链接'): ?>
            <!--友情链接-->
            <?php if ($this->options->links or $this->options->homeLinks && $this->is('index')): ?>
                <section class=" ">
                    <h2><?php echo $GLOBALS['t']['sidebar']['usefulLinks']; ?></h2>
                    <ul aria-label="<?php echo $GLOBALS['t']['sidebar']['usefulLinks']; ?>">
                        <?php if ($this->options->links): ?>
                            <?php $links = json_decode($this->options->links); ?>
                            <?php foreach ($links as $link): ?>
                                <li>
                                    <a href="<?php echo $link->url; ?>" title="<?php echo isset($link->title)?$link->title:$link->name; ?>" target="_blank">
                                        <?php echo $link->name; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($this->options->homeLinks && $this->is('index')): ?>
                            <?php $links = json_decode($this->options->homeLinks); ?>
                            <?php foreach ($links as $link): ?>
                                <li>
                                    <a href="<?php echo $link->url; ?>" title="<?php echo isset($link->title)?$link->title:$link->name; ?>" target="_blank">
                                        <?php echo $link->name; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </section>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($component == '目录' && $GLOBALS['page'] == 'post' && $GLOBALS['post']['directory'] != null): ?>
            <!--用于文章页的章节目录-->
            <section class=" directory">
                <h2><?php echo $GLOBALS['t']['sidebar']['tableOfContents']; ?></h2>
                <?php echo $GLOBALS['post']['directory']; ?>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>
</aside>

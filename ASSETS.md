# YCLite 主题 静态资源清单

> 整理于 2026-08-11。已删除 webpack 残留构建产物 `style-1774276299.css`、`bundle-1774276299.js`（无任何页面引用）。

## CSS 文件（10 个）

| 文件 | 作用 | 首页加载 |
|---|---|---|
| `assets/css/theme.css` | 主样式 | ✅ 60KB |
| `assets/css/icon-font.css` | 图标字体 | ✅ 29KB |
| `assets/css/highlight.css` | 代码高亮样式 | ✅ 2.6KB |
| `assets/css/base.css` | 基础样式 | — |
| `assets/css/components.css` | 组件样式 | — |
| `assets/css/facile.css` | 布局/工具样式 | — |
| `assets/css/facile-v2.css` | 布局/工具样式(v2) | — |
| `assets/css/icon-classes.css` | 图标类名 | — |
| `assets/css/options-panel.css` | 后台选项面板样式 | — |
| `assets/css/style-1774276299.css` | ❌ 已删除（webpack 残留） | — |

## JS 文件（21 个）

### 核心库（首页加载）
| 文件 | 作用 | 体积 |
|---|---|---|
| `assets/js/jquery-3.5.1.min.js` | jQuery | 35KB |
| `assets/js/bootstrap.bundle.min.js` | Bootstrap JS | 26KB |
| `assets/js/app.js` | 主题主逻辑 | 2KB |
| `assets/js/jquery.pjax.js` | PJAX 无刷新跳转 | — |
| `assets/js/highlight.pack.js` | 代码高亮 | 34KB |

### 功能模块（`assets/js/modules/`）
| 文件 | 作用 |
|---|---|
| `modules/ArticleEngagement.js` | 文章互动（点赞等） |
| `modules/AvatarGenerator.js` | 头像生成 |
| `modules/Directory.js` | 目录导航 |
| `modules/Emoji.js` | 表情 |
| `modules/Lightbox.js` | 图片灯箱 |
| `modules/PJAX.js` | PJAX 模块 |
| `modules/ThemeColor.js` | 主题配色切换 |
| `modules/accessibilityInit.js` | 无障碍 |
| `modules/codeHighlightInit.js` | 代码高亮初始化 |

### 其他独立 JS
| 文件 | 作用 |
|---|---|
| `assets/js/ECharts.js` | 图表库 |
| `assets/js/clipboard.min.js` | 复制按钮 |
| `assets/js/qrious.min.js` | 二维码生成 |
| `assets/js/directory-toggle.js` | 目录开关 |
| `assets/js/emoji-init.js` | 表情初始化 |
| `assets/js/options-panel.js` | 后台选项面板 |
| `assets/js/bundle-1774276299.js` | ❌ 已删除（webpack 残留） |

## 首页实际加载量
- CSS+JS 合计约 229KB（gzip 后传输）
- 首屏导航计时：TTFB 60ms / FCP 196ms / Load 220ms（CDP 实测，开启 WebP 缩略图后）

## 图片策略
- 文章列表/首页卡片封面：自动生成 320px WebP 缩略图（`assets/cache/thumbs/`，质量 85），开关 `webpThumbnail=on`
- 文章正文内联图片：保持原图（用户要求）
- 懒加载开关：`imagelazyloading`

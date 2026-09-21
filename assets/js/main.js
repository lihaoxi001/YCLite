// YCLite 入口：常驻仅 theme/nav/totop，其余按需动态 import
import { $ } from './lib/dom.js';
import { init as initTheme } from './features/theme.js';
import { init as initNav } from './features/nav.js';
import { init as initTop } from './features/totop.js';
import { init as initKeys } from './features/comments.js';

initTheme();
initNav();
initTop();
initKeys();

const has = (sel) => !!$(sel);

if (has('.post-content img')) import('./features/lightbox.js').then((m) => m.init());
if (has('.post-content pre code')) import('./features/code.js').then((m) => m.init());
if (has('#emoji-panel')) import('./features/emoji.js').then((m) => m.init());
if (has('.agree-btn')) import('./features/like.js').then((m) => m.init());
if (has('#qr')) import('./features/qr.js').then((m) => m.init());
if (has('#captcha-img')) import('./features/captcha.js').then((m) => m.init());
if (has('.post-content')) import('./features/math.js').then((m) => m.init());
if (has('.directory-link')) import('./features/toc.js').then((m) => m.init());
if (has('.load-more-btn')) import('./features/loadmore.js').then((m) => m.init());

if ('serviceWorker' in navigator && location.protocol.startsWith('http')) {
  const sw = document.body.dataset.sw;
  if (sw) {
    window.addEventListener('load', () => {
      navigator.serviceWorker.register(sw).catch(() => {});
    });
  }
}

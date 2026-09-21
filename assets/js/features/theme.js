import { $, $$ } from '../lib/dom.js';
import { getCookie, setCookie } from '../lib/cookie.js';

export function init() {
  const btn = $('#theme-color-toggle');
  if (!btn) return;
  const icon = $('#theme-color-icon');
  btn.addEventListener('click', () => {
    const html = document.documentElement;
    const next = html.dataset.theme === 'dark' ? 'light' : 'dark';
    apply(next);
  });
  // 仅 auto 模式且访客未手动选过配色时，跟随系统切换实时变化
  // （跟随不写 cookie，否则会锁死后续跟随）
  try {
    const mq = window.matchMedia('(prefers-color-scheme: dark)');
    const follow = (e) => {
      if (getCookie('themeColor')) return;
      if (!document.body.classList.contains('auto-color')) return;
      apply(e.matches ? 'dark' : 'light', false);
    };
    if (typeof mq.addEventListener === 'function') mq.addEventListener('change', follow);
    else if (typeof mq.addListener === 'function') mq.addListener(follow);
  } catch { /* ignore */ }
  function apply(mode, save = true) {
    document.documentElement.dataset.theme = mode;
    document.body.classList.toggle('dark-color', mode === 'dark');
    document.body.classList.toggle('light-color', mode !== 'dark');
    if (save) setCookie('themeColor', mode === 'dark' ? 'dark-color' : 'light-color');
    if (icon) icon.textContent = mode === 'dark' ? '🌙' : '☀️';
    const meta = $('meta[name="color-scheme"]');
    if (meta) meta.content = mode === 'dark' ? 'dark' : 'light';
    const logo = $('#nav-logo');
    if (logo && logo.dataset.logoDark) {
      logo.src = mode === 'dark' ? logo.dataset.logoDark : (logo.dataset.logoLight || logo.src);
    }
    syncCodeTheme(mode);
  }
  btn._applyTheme = apply;
}

function syncCodeTheme(mode) {
  if (!document.body.classList.contains('follow-theme-color')) return;
  document.body.classList.toggle('vs2015', mode === 'dark');
  document.body.classList.toggle('stackoverflow-light', mode !== 'dark');
}

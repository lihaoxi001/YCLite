import { $, on } from '../lib/dom.js';

// 移动端菜单 + 回车/ESC 行为；桌面端下拉用 <details> 零 JS
export function init() {
  const btn = $('#nav-toggle');
  const nav = $('#nav-links');
  if (!btn || !nav) return;
  on(btn, 'click', () => {
    const open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', String(open));
  });
  on(document, 'keydown', (e) => {
    if (e.key === 'Escape' && nav.classList.contains('open')) {
      nav.classList.remove('open');
      btn.setAttribute('aria-expanded', 'false');
      btn.focus();
    }
  });
}

import { $, on } from '../lib/dom.js';

// 键盘 ←/→ 翻页（输入框内不触发）；评论回复跟随交给 TypechoComment
export function init() {
  let typing = false;
  on(document, 'focusin', (e) => {
    typing = !!e.target.closest('input,textarea,[contenteditable]');
  });
  on(document, 'focusout', () => { typing = false; });
  on(document, 'keydown', (e) => {
    if (typing) return;
    const next = $('.pagination .next-page, .post-nav .next-page');
    const prev = $('.pagination .prev-page, .post-nav .prev-page');
    if ((e.key === 'ArrowRight') && next) { next.click(); }
    else if ((e.key === 'ArrowLeft') && prev) { prev.click(); }
  });
  // 回复后平滑滚动到表单
  on(document, 'click', (e) => {
    const r = e.target.closest('.comment-reply a');
    if (!r) return;
    setTimeout(() => {
      const form = $('#comment-form');
      if (form) form.scrollIntoView({ behavior: 'smooth', block: 'center' });
      const ta = $('#textarea');
      if (ta) ta.focus({ preventScroll: true });
    }, 50);
  });
}

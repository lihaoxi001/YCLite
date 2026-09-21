import { $, on } from '../lib/dom.js';

// 点击加载更多：取下一页 HTML，追加卡片，更新隐藏分页；无 JS 时回退数字分页
export function init() {
  const btn = $('.load-more-btn');
  const list = $('.post-list');
  if (!btn || !list) return;
  const t = window.t || {};
  const labelMore = t.loadMore || '加载更多';
  const labelLoading = t.loading || '正在加载...';
  let busy = false;

  const nextUrl = () => {
    // Typecho 把 next-page class 放在 span 上，链接是里面的 a
    const el = list.querySelector('nav .pagination .next-page');
    if (!el) return null;
    const a = el.tagName === 'A' ? el : el.querySelector('a');
    return a ? a.getAttribute('href') : null;
  };

  on(btn, 'click', async () => {
    if (busy) return;
    const url = nextUrl();
    if (!url) {
      btn.hidden = true;
      return;
    }
    busy = true;
    btn.disabled = true;
    btn.textContent = labelLoading;
    try {
      const ctrl = new AbortController();
      const timer = setTimeout(() => ctrl.abort(), 30000);
      const res = await fetch(url, { signal: ctrl.signal, credentials: 'same-origin' });
      clearTimeout(timer);
      if (!res.ok) throw new Error('http ' + res.status);
      const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
      const nav = list.querySelector(':scope > nav');
      doc.querySelectorAll('.post-list > .post-card, .post-list > .post-card-wechat').forEach((c) => {
        list.insertBefore(document.importNode(c, true), nav);
      });
      const freshNav = doc.querySelector('.post-list > nav');
      if (freshNav && nav) nav.replaceWith(document.importNode(freshNav, true));
      if (!nextUrl()) {
        btn.hidden = true;
      } else {
        busy = false;
        btn.disabled = false;
        btn.textContent = labelMore;
      }
    } catch {
      busy = false;
      btn.disabled = false;
      btn.textContent = labelMore;
    }
  });
}

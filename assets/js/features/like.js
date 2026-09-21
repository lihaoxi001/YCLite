import { $, on } from '../lib/dom.js';

export function init() {
  const btn = $('.agree-btn');
  if (!btn || btn.disabled) return;
  on(btn, 'click', async () => {
    btn.disabled = true;
    const t = window.t || {};
    try {
      const ctrl = new AbortController();
      const timer = setTimeout(() => ctrl.abort(), 15000);
      const res = await fetch(btn.dataset.url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'agree=' + encodeURIComponent(btn.dataset.cid),
        signal: ctrl.signal
      });
      clearTimeout(timer);
      const text = (await res.text()).trim();
      if (/^\d+$/.test(text)) {
        const num = btn.querySelector('.agree-num');
        if (num) num.textContent = (t.like || '赞') + ' ' + text;
        const tip = document.createElement('span');
        tip.className = 'like-tip';
        tip.setAttribute('role', 'status');
        tip.textContent = (t.like || '赞') + ' +1';
        const r = btn.getBoundingClientRect();
        tip.style.top = (r.top + window.scrollY - 30) + 'px';
        tip.style.left = (r.left + r.width / 2 - 30) + 'px';
        document.body.appendChild(tip);
        tip.animate(
          [{ transform: 'translateY(0)', opacity: 1 }, { transform: 'translateY(-40px)', opacity: 0 }],
          { duration: 500, easing: 'ease-out' }
        ).onfinish = () => tip.remove();
      } else {
        btn.disabled = false;
      }
    } catch {
      btn.disabled = false;
    }
  });
}

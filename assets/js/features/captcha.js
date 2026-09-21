import { $ } from '../lib/dom.js';

// 评论图片验证码：拉取算式图片，点击刷新，5 分钟自动刷新
export function init() {
  const img = $('#captcha-img');
  const token = $('#captcha-token');
  const box = $('#img-captcha');
  if (!img || !box) return;
  const t = window.t || {};
  let timer = null;
  const failText = () => t.captchaLoadError || '验证码加载失败，点击重试';

  async function load() {
    try {
      const ctrl = new AbortController();
      const timer2 = setTimeout(() => ctrl.abort(), 15000);
      const base = (img.dataset.url || location.origin + '/').replace(/\/?$/, '/');
      const res = await fetch(base + '?action=captcha', { signal: ctrl.signal, credentials: 'same-origin' });
      clearTimeout(timer2);
      const data = await res.json();
      box.querySelector('.captcha-error')?.remove();
      if (data && data.result === 'success') {
        img.hidden = false;
        img.src = 'data:image/png;base64,' + data.image;
        img.alt = t.captchaImageAlt || '';
        if (token) token.value = data.token || '';
      } else {
        showError((data && data.message) || failText());
      }
    } catch {
      showError(failText());
    }
  }
  function showError(msg) {
    box.querySelector('.captcha-error')?.remove();
    img.hidden = true;
    const s = document.createElement('span');
    s.className = 'captcha-error';
    s.setAttribute('role', 'alert');
    s.textContent = msg;
    s.addEventListener('click', load);
    box.appendChild(s);
  }
  img.addEventListener('click', load);
  load();
  if (timer) clearInterval(timer);
  timer = setInterval(load, 300000);
}

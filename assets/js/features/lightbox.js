import { $$, on } from '../lib/dom.js';
import { openDlg, closeDlg } from './dialog.js';

// 轻量灯箱：<dialog> + 切换 + 缩放 + 键盘
export function init() {
  const imgs = $$('.post-content img');
  if (!imgs.length) return;
  imgs.forEach((img, i) => { img.dataset.index = i; });
  let dlg = null, view = null, idx = 0, zoom = 1;
  const t = window.t || {};

  function open(i) {
    idx = i; zoom = 1;
    if (!dlg) {
      dlg = document.createElement('dialog');
      dlg.className = 'lightbox';
      dlg.setAttribute('aria-label', t.closeImage || '图片查看');
      dlg.innerHTML =
        '<img alt="">' +
        '<div class="lightbox-bar">' +
        '<button type="button" class="btn" data-act="prev">‹</button>' +
        '<button type="button" class="btn" data-act="zoom">＋/－</button>' +
        '<button type="button" class="btn" data-act="next">›</button>' +
        '<button type="button" class="btn btn-primary" data-act="close">✕</button>' +
        '</div>';
      document.body.appendChild(dlg);
      view = dlg.querySelector('img');
      on(dlg, 'click', (e) => {
        const b = e.target.closest('button');
        if (!b && e.target === dlg) { close(); return; }
        if (!b) return;
        const act = b.dataset.act;
        if (act === 'close') close();
        else if (act === 'next') show((idx + 1) % imgs.length);
        else if (act === 'prev') show((idx - 1 + imgs.length) % imgs.length);
        else if (act === 'zoom') { zoom = zoom === 1 ? 1.8 : 1; view.style.transform = 'scale(' + zoom + ')'; }
      });
      on(dlg, 'keydown', (e) => {
        if (e.key === 'Escape' && dlg.classList.contains('fallback-open')) { close(); return; }
        if (e.key === 'ArrowRight') show((idx + 1) % imgs.length);
        else if (e.key === 'ArrowLeft') show((idx - 1 + imgs.length) % imgs.length);
      });
      on(view, 'click', () => { zoom = zoom === 1 ? 1.8 : 1; view.style.transform = 'scale(' + zoom + ')'; });
    }
    show(i);
    openDlg(dlg);
  }
  function close() {
    closeDlg(dlg);
  }
  function show(i) {
    idx = i; zoom = 1;
    const src = imgs[i];
    view.style.transform = 'scale(1)';
    view.src = src.dataset.src || src.currentSrc || src.src;
    view.alt = src.alt || '';
  }
  imgs.forEach((img) => on(img, 'click', () => open(Number(img.dataset.index))));
}

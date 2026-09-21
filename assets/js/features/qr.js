import { $, assetUrl, loadScript } from '../lib/dom.js';

// 分享二维码：首次展开分享区才加载 qrious
export function init() {
  const canvas = $('#qr');
  if (!canvas || canvas.dataset.done) return;
  const box = $('#qr-link');
  const render = async () => {
    if (canvas.dataset.done) return;
    canvas.dataset.done = '1';
    try {
      await loadScript(assetUrl('assets/js/qrious.min.js'));
      if (window.QRious) new window.QRious({ element: canvas, value: location.href, size: 150 });
    } catch { /* ignore */ }
  };
  if (!box) { render(); return; }
  if (!box.hasAttribute('hidden')) render();
  new MutationObserver(render).observe(box, { attributes: true, attributeFilter: ['hidden'] });
}

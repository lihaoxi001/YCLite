import { $, $$, on } from '../lib/dom.js';
import { closeDlg } from './dialog.js';

// 桌面目录 scrollspy + 目录链接跳转；dialog 开关由内联即时脚本负责（零等待）
export function init() {
  const links = $$('.directory-link');
  if (!links.length) return;
  const map = new Map(links.map((a) => [a.getAttribute('href'), a]));
  const targets = $$('.title-position');
  const headerH = () => ($('.topbar') ? $('.topbar').offsetHeight : 0);

  on(document, 'click', (e) => {
    const a = e.target.closest('.directory-link');
    if (!a) return;
    const id = a.getAttribute('href');
    const t = $(id);
    if (!t) return;
    e.preventDefault();
    const y = t.getBoundingClientRect().top + window.scrollY - headerH() - 12;
    window.scrollTo({ top: y, behavior: 'smooth' });
    const dlg = $('#toc-modal');
    if (dlg && (dlg.open || dlg.classList.contains('fallback-open'))) closeDlg(dlg);
  });

  if (!targets.length || !('IntersectionObserver' in window)) return;
  let current = null;
  const io = new IntersectionObserver((entries) => {
    entries.forEach((en) => {
      if (en.isIntersecting) current = '#' + en.target.id;
    });
    map.forEach((a) => a.classList.remove('directory-active'));
    const a = current && map.get(current);
    if (a) a.classList.add('directory-active');
  }, { rootMargin: `-${headerH() + 20}px 0px -70% 0px` });
  targets.forEach((t) => io.observe(t));
}

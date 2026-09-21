import { $, on } from '../lib/dom.js';

export function init() {
  const btn = $('#to-top-btn');
  if (!btn) return;
  const toggle = () => btn.toggleAttribute('hidden', window.scrollY < window.innerHeight);
  on(window, 'scroll', toggle, { passive: true });
  toggle();
  on(btn, 'click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    const brand = $('.brand');
    if (brand) brand.focus({ preventScroll: true });
  });
}

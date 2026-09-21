export const $ = (sel, root = document) => root.querySelector(sel);
export const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));
export const on = (el, ev, fn, opt) => el && el.addEventListener(ev, fn, opt);
const scriptCache = new Map();
export function loadScript(src) {
  if (scriptCache.has(src)) return scriptCache.get(src);
  const p = new Promise((resolve, reject) => {
    const s = document.createElement('script');
    s.src = src; s.defer = true;
    s.onload = () => resolve();
    s.onerror = () => reject(new Error('load fail: ' + src));
    document.head.appendChild(s);
  });
  scriptCache.set(src, p);
  return p;
}
export function assetUrl(path) {
  const base = document.body.dataset.themeUrl || '';
  return base ? base.replace(/\/?$/, '/') + path : path;
}

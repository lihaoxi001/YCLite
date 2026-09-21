import { $, $$, on } from '../lib/dom.js';

// Emoji 面板：点开才加载数据
export async function init() {
  const panel = $('#emoji-panel');
  const btn = $('#show-emoji-btn');
  const area = $('#textarea');
  if (!panel || !btn || !area) return false;
  // 开关由内联即时脚本负责（零等待）；这里只观察首次打开，按需加载数据
  let loaded = false;
  const loadFirst = async () => {
    if (loaded) return;
    loaded = true;
    try {
      const mod = await import('./emoji-data.js');
      render(mod.default || mod.emojiList || mod, 'smileys');
    } catch { /* ignore */ }
  };
  if (!panel.hasAttribute('hidden')) loadFirst();
  new MutationObserver(loadFirst).observe(panel, { attributes: true, attributeFilter: ['hidden'] });
  on(document, 'click', (e) => {
    if (!panel.hasAttribute('hidden') && !e.target.closest('#emoji-box')) {
      panel.setAttribute('hidden', '');
      btn.setAttribute('aria-expanded', 'false');
    }
  });
  on(panel, 'click', (e) => {
    const cat = e.target.closest('[data-classification]');
    if (cat) {
      $$('#emoji-classification button').forEach((b) => {
        b.classList.remove('selected');
        b.setAttribute('aria-checked', 'false');
      });
      cat.classList.add('selected');
      cat.setAttribute('aria-checked', 'true');
      import('./emoji-data.js').then((mod) => render(mod.default || mod.emojiList || mod, cat.dataset.classification)).catch(() => {});
      return;
    }
    const em = e.target.closest('#emoji-list button');
    if (em) {
      insertAtCursor(area, em.textContent);
      area.focus();
    }
  });
  on(panel, 'keydown', (e) => {
    if (e.key === 'Escape') {
      panel.setAttribute('hidden', '');
      btn.setAttribute('aria-expanded', 'false');
      area.focus();
    }
  });
  function insertAtCursor(area, text) {
    const start = area.selectionStart ?? area.value.length;
    const end = area.selectionEnd ?? area.value.length;
    area.value = area.value.slice(0, start) + text + area.value.slice(end);
    const pos = start + text.length;
    area.setSelectionRange(pos, pos);
  }
  function render(list, cat) {
    const arr = (list && list[cat]) || [];
    const box = $('#emoji-list');
    box.innerHTML = '';
    const frag = document.createDocumentFragment();
    arr.forEach((c) => {
      const b = document.createElement('button');
      b.type = 'button';
      b.textContent = c;
      b.setAttribute('aria-label', 'emoji ' + c);
      frag.appendChild(b);
    });
    box.appendChild(frag);
    const title = $('#emoji-title');
    if (title) title.textContent = cat;
  }
}

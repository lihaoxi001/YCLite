import { $$, assetUrl, loadScript } from '../lib/dom.js';

// 代码高亮 + 复制；仅当存在 pre code 时加载
export async function init() {
  const blocks = $$('.post-content pre').filter((pre) => pre.querySelector('code'));
  if (!blocks.length) return;
  try {
    await loadScript(assetUrl('assets/js/highlight.pack.js'));
  } catch { return; }
  if (!window.hljs) return;
  const t = window.t || {};
  blocks.forEach((pre, i) => {
    const code = pre.querySelector('code');
    try {
      if (window.hljs.highlightElement) window.hljs.highlightElement(code);
      else if (window.hljs.highlightBlock) window.hljs.highlightBlock(code);
    } catch { /* ignore */ }
    code.id = 'code-' + i;
    if (!pre.querySelector('.copy-btn')) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'copy-btn';
      btn.textContent = t.copyCode || '复制';
      btn.setAttribute('aria-label', t.copyCode || '复制代码');
      btn.addEventListener('click', async () => {
        const done = (ok) => {
          btn.textContent = ok ? (t.copySuccess || '已复制') : (t.copyError || '失败');
          setTimeout(() => { btn.textContent = t.copyCode || '复制'; }, 1200);
        };
        try {
          await navigator.clipboard.writeText(code.innerText);
          done(true);
        } catch {
          const ta = document.createElement('textarea');
          ta.value = code.innerText;
          document.body.appendChild(ta);
          ta.select();
          try { done(document.execCommand('copy')); } catch { done(false); }
          ta.remove();
        }
      });
      pre.prepend(btn);
    }
  });
}

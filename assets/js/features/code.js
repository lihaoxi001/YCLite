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
          // Clipboard API 不可用（如非安全上下文）：execCommand 已废弃不再使用，
          // 改为选中代码文本，提示访客手动复制
          try {
            const range = document.createRange();
            range.selectNodeContents(code);
            const sel = getSelection();
            sel.removeAllRanges();
            sel.addRange(range);
          } catch { /* ignore */ }
          btn.textContent = t.copyManual || '已选中，请手动复制';
          setTimeout(() => { btn.textContent = t.copyCode || '复制'; }, 2000);
        }
      });
      pre.prepend(btn);
    }
  });
}

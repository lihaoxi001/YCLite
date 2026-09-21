import { $, assetUrl, loadScript } from '../lib/dom.js';

// 公式：只认 $$..$$、\[..\]、\(..\)；裸 $ 留给美元价格，不当数学分隔符
const PATTERNS = [/\$\$[\s\S]+?\$\$/, /\\\[[\s\S]+?\\\]/, /\\\([\s\S]+?\\\)/];
export async function init() {
  const post = $('.post-content');
  if (!post) return;
  const text = post.cloneNode(true);
  text.querySelectorAll('pre,code').forEach((el) => el.remove());
  if (!PATTERNS.some((p) => p.test(text.textContent))) return;
  // 不需要语音/无障碍富化：关掉 SRE worker，避免下发 sre 文件和控制台报错
  // inlineMath 只保留 \(..\)（MathJax 默认），裸 $ 一律视为普通文本（价格）
  window.MathJax = {
    tex: { inlineMath: [['\\(', '\\)']], displayMath: [['$$', '$$'], ['\\[', '\\]']] },
    chtml: { scale: 1, minScale: 0.85, matchFontHeight: true },
    options: {
      enableSpeech: false,
      enableBraille: false,
      enableEnrichment: false,
      menuOptions: { settings: { enrich: false, speech: false, braille: false } }
    }
  };
  for (const url of [
    'https://cdnjs.cloudflare.com/ajax/libs/mathjax/4.0.0/tex-mml-chtml.js',
    'https://cdn.jsdelivr.net/npm/mathjax@4.0.0/tex-mml-chtml.js'
  ]) {
    try {
      await loadScript(url);
      if (window.MathJax && window.MathJax.typeset) {
        window.MathJax.typeset([post]);
        return;
      }
    } catch { /* try next CDN */ }
  }
}

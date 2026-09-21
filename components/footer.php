<footer class="footer">
    <div class="wrap">
        <?php if ($this->options->icp): ?>
            <nav><?php $this->options->icp(); ?></nav>
        <?php endif; ?>
        <p class="muted mono">YCLite · native CSS+JS · no jQuery</p>
    </div>
</footer>

<button class="fab" id="to-top-btn" type="button" hidden aria-label="<?php echo $GLOBALS['t']['scrollToTop']; ?>" title="<?php echo $GLOBALS['t']['scrollToTop']; ?>">
    <i class="icon-arrow-up"></i>
</button>

<?php
// v2 资源指纹
$__v = function ($f) {
    $p = __DIR__ . '/../' . $f;
    return $f . '?v=' . (is_file($p) ? filemtime($p) : '1');
};
$__themeUrl = rtrim($this->options->themeUrl, '/');
?>
<script>
/* v2 即时交互：随 HTML 解析执行，零网络等待；ES 模块只做增强，不重复绑定 */
(function () {
  function closeToc(dlg) {
    if (dlg.classList.contains('fallback-open')) {
      dlg.classList.remove('fallback-open');
      dlg.removeAttribute('open');
    } else if (dlg.open) {
      dlg.close();
    }
  }
  function openToc(dlg) {
    if (typeof dlg.showModal === 'function') {
      if (!dlg.open) dlg.showModal();
    } else {
      dlg.classList.add('fallback-open');
      dlg.setAttribute('open', '');
    }
  }
  document.addEventListener('click', function (e) {
    var t = e.target.closest('[data-collapse-target]');
    if (t) {
      var box = document.querySelector(t.getAttribute('data-collapse-target'));
      if (box) {
        var show = box.hasAttribute('hidden');
        box.toggleAttribute('hidden');
        t.setAttribute('aria-expanded', String(show));
      }
      return;
    }
    // 上一篇/下一篇整卡可点（点链接本身走原生，保留新标签页；点卡片空白处跳转）
    var pn = e.target.closest('.post-nav .previous, .post-nav .next');
    if (pn) {
      if (e.target.closest('a')) return;
      if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      var link = pn.querySelector('a');
      if (link && link.getAttribute('href')) {
        location.href = link.getAttribute('href');
      }
      return;
    }
    if (e.target.closest('#directory-btn')) {
      var dlg = document.getElementById('toc-modal');
      if (dlg) openToc(dlg);
      return;
    }
    if (e.target.closest('#show-emoji-btn')) {
      var p = document.getElementById('emoji-panel');
      var eb = document.getElementById('show-emoji-btn');
      if (p && eb) {
        var s = p.hasAttribute('hidden');
        p.toggleAttribute('hidden');
        eb.setAttribute('aria-expanded', String(s));
      }
      return;
    }
    var toc = document.getElementById('toc-modal');
    if (toc && (toc.open || toc.classList.contains('fallback-open'))) {
      if (e.target === toc || e.target.closest('#toc-modal form button')) closeToc(toc);
    }
  });
})();
</script>
<script type="module" src="<?php echo $__themeUrl . '/' . $__v('assets/js/main.js'); ?>"></script>
<?php if ($this->options->bodyHTML): ?>
    <?php $this->options->bodyHTML(); ?>
<?php endif; ?>

<?php $this->footer(); ?>
</body>
</html>

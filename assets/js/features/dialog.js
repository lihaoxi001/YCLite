// dialog 公共操作（toc.js / lightbox.js 共用；内联即时脚本里有一份精简版，改这里记得同步）
export function openDlg(dlg) {
  if (typeof dlg.showModal === 'function') {
    if (!dlg.open) dlg.showModal();
  } else {
    dlg.classList.add('fallback-open');
    dlg.setAttribute('open', '');
  }
}

export function closeDlg(dlg) {
  if (dlg.classList.contains('fallback-open')) {
    dlg.classList.remove('fallback-open');
    dlg.removeAttribute('open');
  } else if (dlg.open) {
    dlg.close();
  }
}

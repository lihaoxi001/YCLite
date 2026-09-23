export function getCookie(name) {
  const m = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
  return m ? decodeURIComponent(m[1]) : null;
}
export function setCookie(name, value, days = 0) {
  // days<=0 为会话 cookie（关浏览器失效）；>0 则为持久 cookie
  let cookie = name + '=' + encodeURIComponent(value) + ';path=/';
  if (days > 0) {
    const d = new Date(Date.now() + days * 864e5);
    cookie += ';expires=' + d.toUTCString();
  }
  document.cookie = cookie;
}

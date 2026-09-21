export function getCookie(name) {
  const m = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
  return m ? decodeURIComponent(m[1]) : null;
}
export function setCookie(name, value, days = 180) {
  const d = new Date(Date.now() + days * 864e5);
  document.cookie = name + '=' + encodeURIComponent(value) + ';path=/;expires=' + d.toUTCString();
}

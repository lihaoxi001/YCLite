// YCLite SW：只运行时缓存带指纹的静态资源，HTML 永远走网络
const CACHE = 'yclite-static-v1';
const STATIC_RE = /\.(?:css|js|svg|woff2?|png|jpe?g|gif|webp|ico)(\?.*)?$/i;

self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys()
      .then((keys) => Promise.all(keys.filter((k) => k !== CACHE).map((k) => caches.delete(k))))
      .then(() => self.clients.claim())
  );
});
self.addEventListener('fetch', (event) => {
  const { request } = event;
  if (request.method !== 'GET') return;
  const url = new URL(request.url);
  if (url.origin !== location.origin) return;
  if (!STATIC_RE.test(url.pathname)) return;
  // 无指纹（无 ?v=）的不缓存，避免发版滞留
  if (!url.search.includes('v=')) return;
  event.respondWith(
    caches.match(request).then((hit) => {
      const net = fetch(request).then((res) => {
        if (res && res.status === 200) {
          const copy = res.clone();
          caches.open(CACHE).then((c) => c.put(request, copy));
        }
        return res;
      }).catch(() => hit);
      return hit || net;
    })
  );
});

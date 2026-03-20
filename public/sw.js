/**
 * SOFTPRO SMS - Service Worker
 * Cache version: bump on deploy to invalidate old caches
 */
const CACHE_VERSION = 'v1';
const STATIC_CACHE = 'sms-static-' + CACHE_VERSION;
const PAGE_CACHE = 'sms-pages-' + CACHE_VERSION;

// Static assets to cache on install
const STATIC_ASSETS = [
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png',
  '/build/manifest.json'
];

// Install: cache static assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) => {
      return cache.addAll(STATIC_ASSETS).catch(() => {
        // Ignore if some assets fail (e.g. build manifest may not exist)
      });
    }).then(() => self.skipWaiting())
  );
});

// Activate: remove old caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) => {
      return Promise.all(
        keys.filter((k) => k.startsWith('sms-') && k !== STATIC_CACHE && k !== PAGE_CACHE)
          .map((k) => caches.delete(k))
      );
    }).then(() => self.clients.claim())
  );
});

// Fetch: network-first for pages, cache-first for static
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);
  const isSameOrigin = url.origin === location.origin;

  if (!isSameOrigin) return;
  if (event.request.method !== 'GET') return;

  // Static assets: cache-first
  if (url.pathname.match(/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2)$/i) ||
      url.pathname.startsWith('/build/') ||
      url.pathname.startsWith('/icons/') ||
      url.pathname === '/manifest.json') {
    event.respondWith(
      caches.match(event.request).then((cached) => {
        return cached || fetch(event.request).then((res) => {
          const clone = res.clone();
          caches.open(STATIC_CACHE).then((cache) => cache.put(event.request, clone));
          return res;
        });
      })
    );
    return;
  }

  // HTML pages: network-first (always fresh for auth, forms)
  if (event.request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      fetch(event.request).then((res) => {
        const clone = res.clone();
        if (res.ok && !url.pathname.startsWith('/admin') && !url.pathname.startsWith('/login')) {
          caches.open(PAGE_CACHE).then((cache) => cache.put(event.request, clone));
        }
        return res;
      }).catch(() => {
        return caches.match(event.request).then((cached) => {
          return cached || caches.match('/');
        });
      })
    );
    return;
  }

  // Other requests: bypass
});

const CACHE = 'travel-v1';

const STATIC_ASSETS = [
    '/plan/assets/style.css',
    '/plan/assets/scripts.js',
];

// Instalar: pre-cachear assets estáticos
self.addEventListener('install', function (e) {
    e.waitUntil(
        caches.open(CACHE).then(function (c) { return c.addAll(STATIC_ASSETS); })
    );
    self.skipWaiting();
});

// Activar: borrar cachés antiguas
self.addEventListener('activate', function (e) {
    e.waitUntil(
        caches.keys().then(function (keys) {
            return Promise.all(
                keys.filter(function (k) { return k !== CACHE; })
                    .map(function (k) { return caches.delete(k); })
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', function (e) {
    var url = new URL(e.request.url);

    // Sólo interceptar GET del mismo origen
    if (e.request.method !== 'GET' || url.origin !== self.location.origin) return;

    // Assets estáticos (CSS, JS, imágenes): cache first
    if (/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff2?)$/.test(url.pathname)) {
        e.respondWith(
            caches.match(e.request).then(function (cached) {
                return cached || fetch(e.request).then(function (res) {
                    var clone = res.clone();
                    caches.open(CACHE).then(function (c) { c.put(e.request, clone); });
                    return res;
                });
            })
        );
        return;
    }

    // Páginas PHP del plan: network first, fallback a caché
    if (url.pathname.startsWith('/plan/') && !url.pathname.includes('logout')) {
        e.respondWith(
            fetch(e.request).then(function (res) {
                var clone = res.clone();
                caches.open(CACHE).then(function (c) { c.put(e.request, clone); });
                return res;
            }).catch(function () {
                return caches.match(e.request);
            })
        );
    }
});

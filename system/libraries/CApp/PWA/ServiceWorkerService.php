<?php

class CApp_PWA_ServiceWorkerService {
    public function generate($theme) {
        $output = '';
        $output .= 'var staticCacheName = "pwa-v" + new Date().getTime();' . PHP_EOL;
        $output .= 'var filesToCache = ' . json_encode($this->getAssetUrl($theme)) . PHP_EOL;
        $output .= $this->installScript() . PHP_EOL;

        return $output;
    }

    protected function getAssetUrl($theme) {
        return [];
        // c::manager()->theme()->setTheme($theme);
        // c::app()->registerCoreModules();
        // $jsUrl = CManager::asset()->getAllJsFileUrl();
        // $cssUrl = CManager::asset()->getAllCssFileUrl();

        // return array_merge($cssUrl, $jsUrl);
    }

    protected function installScript() {
        return <<<JAVASCRIPT
// Cache on install
self.addEventListener("install", event => {
    this.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Clear cache on activate
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Serve from Cache
self.addEventListener("fetch", event => {
    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                return caches.match('offline');
            })
    )
});
JAVASCRIPT;
    }
}

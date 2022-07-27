<?php

class CApp_PWA {
    protected $enabled = false;

    protected $startUrl = '';

    public function enable($startUrl, $theme) {
        if (!$this->enabled) {
            $startUrl = '/' . trim($startUrl, '/') . '/';
            $this->startUrl = $startUrl;
            c::router()->get($this->manifestUrl(), function () use ($startUrl) {
                $output = (new CApp_PWA_ManifestService())->generate($startUrl);

                return c::response()->json($output);
            });
            c::router()->get($this->offlineUrl(), function () {
                return c::view('cresenity.pwa.offline');
            });
            c::router()->get($this->serviceWorkerUrl(), function () use ($theme) {
                $output = (new CApp_PWA_ServiceWorkerService())->generate($theme);

                return c::response($output, 200, [
                    'Content-Type' => 'text/javascript',
                ]);
            });
            $this->enabled = true;
        }
    }

    /**
     * @return string
     */
    public function startUrl() {
        return $this->startUrl;
    }

    /**
     * @return string
     */
    public function manifestUrl() {
        return $this->startUrl . 'manifest.json';
    }

    /**
     * @return string
     */
    public function offlineUrl() {
        return $this->startUrl . 'offline';
    }

    /**
     * @return string
     */
    public function serviceWorkerUrl() {
        return $this->startUrl . 'serviceworker.js';
    }

    /**
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }
}

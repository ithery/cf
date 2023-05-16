<?php

class CApp_PWA {
    use CApp_PWA_Trait_GroupConfigTrait;

    protected $group;

    protected $enabled = false;

    protected $startUrl = '';

    protected $debug = true;

    public function __construct($group = 'default') {
        $this->group = $group;
        $startUrl = $this->getGroupConfig('start_url', '/');
        $startUrl = '/' . trim($startUrl, '/') . '/';
        $this->startUrl = $startUrl;
        $this->debug = $this->getGroupConfig('debug', true);
    }

    public function enable() {
        $startUrl = $this->startUrl;
        $theme = $this->getGroupConfig('theme');
        if (!$this->enabled) {
            c::router()->get($this->manifestUrl(), function () {
                $output = (new CApp_PWA_ManifestService($this->group))->generate();

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

    public function isDebug() {
        return $this->debug;
    }
}

<?php

class CJavascript_CresJs {
    protected $originalConfig;

    protected $config;

    protected $theme;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct() {
        $this->originalConfig = CF::config('cresjs');
        $this->theme = null;
        $this->buildConfig();
    }

    protected function buildConfig() {
        $currentTheme = c::manager()->theme()->getCurrentTheme();
        if ($this->theme === null || $this->theme != $currentTheme) {
            $config = $this->originalConfig;
            $themeConfig = $this->getThemeConfig($currentTheme);
            $config = carr::merge($config, $themeConfig);
            if (isset($config['themes'])) {
                unset($config['themes']);
            }
            $this->theme = $currentTheme;
            $this->config = $config;
        }
    }

    public function getConfig() {
        $this->buildConfig();

        return $this->config;
    }

    public function get($key) {
        $this->buildConfig();

        return carr::get($this->config, $key);
    }

    public function getThemeConfig($theme = null) {
        if ($theme === null) {
            $theme = $this->theme ?: c::manager()->theme()->getCurrentTheme();
        }

        return carr::get($this->originalConfig, 'themes.' . $theme, []);
    }
}

<?php

use Symfony\Component\Finder\Finder;

class CFConfig {
    public static function bootstrap() {
        $items = [];
        $cached = self::getCachedConfigPath();
        $loadedFromCache = false;
        if (file_exists($cached)) {
            $items = require $cached;
            $loadedFromCache = true;
        }
        $repository = $loadedFromCache ? CConfig::manager()->newRepository($items) : CConfig::manager()->repository();
        if (!$loadedFromCache) {
            self::loadConfiguration($repository);
        }

        $timezone = $repository->get('app.timezone');

        // Set default timezone, due to increased validation of date settings
        // which cause massive amounts of E_NOTICEs to be generated in PHP 5.2+
        date_default_timezone_set(empty($timezone) ? date_default_timezone_get() : $timezone);

        // Load locales
        $locale = $repository->get('app.locale');
        $fallbackLocale = $repository->get('app.fallback_locale');

        CF::setLocale($locale);
        CF::setFallbackLocale($fallbackLocale);

        mb_internal_encoding('UTF-8');
    }

    public static function loadConfiguration(CConfig_Repository $repository) {
        $allFiles = self::getConfigurationFiles();
        foreach ($allFiles as $configKey => $files) {
            self::loadConfigurationFiles($configKey, $files, $repository);
        }
    }

    public static function loadConfigurationFiles($configKey, array $files, CConfig_Repository $repository) {
        $configs = [];
        foreach ($files as $path) {
            $config = require $path;
            if (!is_array($config)) {
                throw new Exception(c::__('Invalid config format in :file', ['file' => str_replace(DOCROOT, '', $path)]));
            } else {
                $configs = array_merge($configs, $config);
            }
        }
        $repository->set($configKey, $configs);
    }

    public static function loadConfigurationFile($configKey, CConfig_Repository $repository) {
    }

    public static function getConfigurationFiles() {
        $files = [];
        $paths = array_reverse(CF::paths());
        // if (CF::domain() == 'tribeliopage.dev.ittron.co.id') {
        //     cdbg::dd($paths, CF::getSharedApp());
        // }
        foreach ($paths as $path) {
            $configPath = $path . 'config' . DS;
            if (is_dir($configPath)) {
                foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
                    $directory = self::getNestedDirectory($file, $configPath);
                    $configKey = basename($file->getRealPath(), '.php');
                    if (!isset($files[$configKey])) {
                        $files[$configKey] = [];
                    }
                    $files[$configKey][$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
                }
            }
        }

        return $files;
    }

    public static function getCachedConfigPath() {
        $appCode = CF::appCode();
        if ($appCode) {
            return DOCROOT . 'temp/cache/' . CF::appCode() . '/config.php';
        }

        return null;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param \SplFileInfo $file
     * @param string       $configPath
     *
     * @return string
     */
    protected static function getNestedDirectory(SplFileInfo $file, $configPath) {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }
}

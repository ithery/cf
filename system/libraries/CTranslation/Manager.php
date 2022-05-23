<?php

class CTranslation_Manager {
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct() {
        $config = CF::config('translation');
        $this->config = $config;
        $this->scanner = new CTranslation_Scanner(carr::get($config, 'scan_paths'), carr::get($config, 'translation_methods'));
    }

    /**
     * @return CTranslation_DriverAbstract
     */
    public function resolve() {
        $driver = $this->config['driver'];
        $driverResolver = cstr::studly($driver);
        $method = "resolve{$driverResolver}Driver";

        if (!method_exists($this, $method)) {
            throw new \InvalidArgumentException("Invalid driver [${driver}]");
        }

        return $this->{$method}();
    }

    /**
     * @return CTranslation_Driver_FileDriver
     */
    protected function resolveFileDriver() {
        return new CTranslation_Driver_FileDriver(c::appRoot('default' . DS . 'i18n'), CF::config('app.locale'), $this->scanner);
    }

    protected function resolveDatabaseDriver() {
        return new CTranslation_Driver_DatabaseDriver(CF::config('app.locale'), $this->scanner);
    }
}

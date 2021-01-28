<?php

class CDatabase_Config {
    protected $config;

    protected $driver;

    protected $host;

    protected $port;

    protected $username;

    protected $password;

    public function __construct($options) {
        $config = $options;
        if (is_string($options)) {
            //may url or connection name, test for connection name

            $config = CF::config('database.' . $options);
            if ($config == null || is_string($config)) {
                if ($config = null) {
                    $config = $options;
                }
                //may config is dsn string
                $urlParser = new CDatabase_ConfigurationUrlParser();
                $config = $urlParser->parseConfiguration($config);
            }
        }
    }

    public function getReadConfig() {
    }

    public function getWriteConfig() {
    }
}

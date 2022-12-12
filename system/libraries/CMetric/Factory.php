<?php

class CMetric_Factory {
    /**
     * @var CMetric_Factory
     */
    private static $instance;

    /**
     * @return CMetric_Factory
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param array $options
     *
     * @return InfluxDB
     */
    public function createInfluxdbDriver(array $options) {
        return new CMetric_Driver_InfluxDBDriver($options);
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     * @param mixed  $config
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function createDriver($driver, array $config) {
        $method = 'create' . cstr::studly($driver) . 'Driver';
        if (method_exists($this, $method)) {
            return $this->$method($config);
        }

        throw new InvalidArgumentException("Driver [${driver}] not supported.");
    }
}

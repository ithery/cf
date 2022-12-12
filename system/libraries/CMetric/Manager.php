<?php

class CMetric_Manager {
    /**
     * The array of created "connections".
     *
     * @var array
     */
    protected $drivers = [];

    /**
     * @var CMetric_Manager
     */
    private static $instance;

    /**
     * @return CMetric_Manager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @return string
     */
    public function getDefaultConnection() {
        $connectionName = CF::config('metric.default');
        if ($connectionName == null) {
            $connectionName = 'null';
        }

        return $connectionName;
    }

    /**
     * Get a driver instance.
     *
     * @param null|string $connection
     *
     * @throws \InvalidArgumentException
     *
     * @return CMetric_DriverAbstract
     */
    public function driver($connection = null) {
        $connection = $connection ?: $this->getDefaultConnection();
        if (is_null($connection)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL metric connection for [%s].',
                static::class
            ));
        }

        $config = CF::config('metric.connection.' . $connection);
        $driver = carr::get($config, 'driver');
        if (is_null($driver)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL metric driver for connection [%s].',
                $connection
            ));
        }

        // If the given driver has not been created before, we will create the instances
        // here and cache it so we can return it next time very quickly. If there is
        // already a driver created by this name, we'll just return that instance.
        if (!isset($this->drivers[$connection])) {
            $this->drivers[$connection] = CMetric_Factory::instance()->createDriver($driver, $config);
        }

        return $this->drivers[$connection];
    }
}

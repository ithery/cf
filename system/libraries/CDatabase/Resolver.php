<?php

class CDatabase_Resolver implements CDatabase_ResolverInterface {
    protected static $instance;

    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if (static::$instance == null) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$domain])) {
            $file = CF::getFile('config', 'database', $domain);
            $allConfig = include $file;
            static::$instance[$domain] = new static(array_keys($allConfig), $domain);
        }
        return static::$instance[$domain];
    }

    /**
     * All of the registered connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The current domain name.
     *
     * @var string
     */
    protected $domain;

    /**
     * The default connection name.
     *
     * @var string
     */
    protected $default;

    /**
     * Create a new connection resolver instance.
     *
     * @param array      $connections
     * @param null|mixed $domain
     *
     * @return void
     */
    public function __construct(array $configs = [], $domain = null) {
        if ($domain == null) {
            $this->domain = CF::domain();
        }
        foreach ($configs as $name => $config) {
            $this->addConfig($name, $config);
        }

        if (array_key_exists('default', $configs)) {
            $this->setDefaultConnection('default');
        }
    }

    /**
     * Get a database connection instance.
     *
     * @param string $name
     *
     * @return CDatabase
     */
    public function connection($name = null) {
        if (is_null($name)) {
            $name = $this->getDefaultConnection();
        }

        return CDatabase::instance($name, null, $this->domain);
    }

    /**
     * Add a connection to the resolver.
     *
     * @param string $name
     * @param array  $config
     *
     * @return void
     */
    public function addConfig($name, $config) {
        $this->connections[$name] = $config;
    }

    /**
     * Check if a connection has been registered.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasConnection($name) {
        return isset($this->connections[$name]);
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection() {
        return $this->default;
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection($name) {
        $this->default = $name;
    }
}

<?php
class CDatabase_ConnectionFactory {
    protected $connectors = [];

    /**
     * Singleton Instance.
     *
     * @var CDatabase_ConnectionFactory
     */
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Create a new connection factory instance.
     *
     * @return void
     */
    public function __construct() {
    }

    /**
     * Establish a PDO connection based on the configuration.
     *
     * @param array       $config
     * @param null|string $name
     *
     * @return CDatabase_Connection
     */
    public function make(array $config, $name = null) {
        $config = $this->parseConfig($config, $name);

        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }

        return $this->createSingleConnection($config);
    }

    /**
     * Parse and prepare the database configuration.
     *
     * @param array  $config
     * @param string $name
     *
     * @return array
     */
    protected function parseConfig(array $config, $name) {
        return carr::add(carr::add($config, 'prefix', ''), 'name', $name);
    }

    /**
     * Create a single database connection instance.
     *
     * @param array $config
     *
     * @return CDatabase_Connection
     */
    protected function createSingleConnection(array $config) {
        $driver = $this->createDriverResolver($config);

        return $this->createConnection(
            $config['driver'],
            $driver,
            $config['database'],
            $config['prefix'],
            $config
        );
    }

    /**
     * Create a read / write database connection instance.
     *
     * @param array $config
     *
     * @return CDatabase_Connection
     */
    protected function createReadWriteConnection(array $config) {
        $connection = $this->createSingleConnection($this->getWriteConfig($config));

        return $connection->setReadDriver($this->createReadDriver($config));
    }

    /**
     * Create a new PDO instance for reading.
     *
     * @param array $config
     *
     * @return \Closure
     */
    protected function createReadDriver(array $config) {
        return $this->createDriverResolver($this->getReadConfig($config));
    }

    /**
     * Get the read configuration for a read / write connection.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getReadConfig(array $config) {
        return $this->mergeReadWriteConfig(
            $config,
            $this->getReadWriteConfig($config, 'read')
        );
    }

    /**
     * Get the write configuration for a read / write connection.
     *
     * @param array $config
     *
     * @return array
     */
    protected function getWriteConfig(array $config) {
        return $this->mergeReadWriteConfig(
            $config,
            $this->getReadWriteConfig($config, 'write')
        );
    }

    /**
     * Get a read / write level configuration.
     *
     * @param array  $config
     * @param string $type
     *
     * @return array
     */
    protected function getReadWriteConfig(array $config, $type) {
        return isset($config[$type][0])
                        ? carr::random($config[$type])
                        : $config[$type];
    }

    /**
     * Merge a configuration for a read / write connection.
     *
     * @param array $config
     * @param array $merge
     *
     * @return array
     */
    protected function mergeReadWriteConfig(array $config, array $merge) {
        return carr::except(array_merge($config, $merge), ['read', 'write']);
    }

    /**
     * Create a new Closure that resolves to a PDO instance.
     *
     * @param array $config
     *
     * @return \Closure
     */
    protected function createDriverResolver(array $config) {
        return array_key_exists('host', $config)
            ? $this->createDriverResolverWithHosts($config)
            : $this->createDriverResolverWithoutHosts($config);
    }

    /**
     * Create a new Closure that resolves to a PDO instance with a specific host or an array of hosts.
     *
     * @param array $config
     *
     * @throws \PDOException
     *
     * @return \Closure
     */
    protected function createDriverResolverWithHosts(array $config) {
        return function () use ($config) {
            $e = null;
            foreach (carr::shuffle($hosts = $this->parseHosts($config)) as $key => $host) {
                $config['host'] = $host;

                try {
                    return $this->createConnector($config)->connect($config);
                } catch (PDOException $e) {
                    continue;
                }
            }

            throw $e;
        };
    }

    /**
     * Parse the hosts configuration item into an array.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseHosts(array $config) {
        $hosts = carr::wrap(carr::get($config, 'host'));
        if (empty($hosts)) {
            throw new InvalidArgumentException('Database hosts array is empty.');
        }

        return $hosts;
    }

    /**
     * Create a new Closure that resolves to a PDO instance where there is no configured host.
     *
     * @param array $config
     *
     * @return \Closure
     */
    protected function createDriverResolverWithoutHosts(array $config) {
        return function () use ($config) {
            return $this->createConnector($config)->connect($config);
        };
    }

    /**
     * Create a connector instance based on the configuration.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \CDatabase_Connector
     */
    public function createConnector(array $config) {
        if (!isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }

        if (c::container()->bound($key = "db.connector.{$config['driver']}")) {
            return c::container()->make($key);
        }

        switch ($config['driver']) {
            case 'mysqli':
                return new CDatabase_Connector_MySqliConnector();
            // case 'mongodb':
            //     return new CDatabase_Connector_MongoDBConnector();
            // case 'pdo.mysql':
            //     return new CDatabase_Connector_PDOConnector_MySqlConnector();
        }

        throw new InvalidArgumentException("Unsupported driver [{$config['driver']}].");
    }

    /**
     * Create a new connection instance.
     *
     * @param string        $driver
     * @param \PDO|\Closure $connection
     * @param string        $database
     * @param string        $prefix
     * @param array         $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \CDatabase_Connection
     */
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = []) {
        if ($resolver = CDatabase_Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }

        switch ($driver) {
            case 'mysqli':
                return new CDatabase_Connection_MySqliConnection($connection, $database, $prefix, $config);
            // case 'mongodb':
            //     return new CDatabase_Connection_MongoDBConnection($connection, $database, $prefix, $config);
            // case 'pdo.mysql':
            //     return new CDatabase_Connection_PDOConnection_MysqlConnection($connection, $database, $prefix, $config);
        }

        throw new InvalidArgumentException("Unsupported driver [{$driver}].");
    }
}

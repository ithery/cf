<?php

use Doctrine\DBAL\Types\Type;

class CDatabase_Manager implements CDatabase_Contract_ConnectionResolverInterface {
    use CTrait_Macroable {
        __call as macroCall;
    }

    protected $defaultConnection;

    /**
     * The active connection instances.
     *
     * @var array<string, \CDatabase_Connection>
     */
    protected $connections = [];

    /**
     * The callback to be executed to reconnect to a database.
     *
     * @var callable
     */
    protected $reconnector;

    /**
     * The custom Doctrine column types.
     *
     * @var array<string, array>
     */
    protected $doctrineTypes = [];

    /**
     * The custom connection resolvers.
     *
     * @var array<string, callable>
     */
    protected $extensions = [];

    /**
     * @var array
     */
    protected $config;

    /**
     * @var CDatabase_Manager
     */
    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Create a new database manager instance.
     *
     * @return void
     */
    public function __construct() {
        $this->reconnector = function ($connection) {
            $this->reconnect($connection->getNameWithReadWriteType());
        };
        $this->config = CF::config('database');

        $defaultConnection = carr::get($this->config, 'default');
        if (is_array($defaultConnection)) {
            $defaultConnection = 'default';
        }
        $this->defaultConnection = $defaultConnection;
    }

    /**
     * Get a database connection instance.
     *
     * @param null|string $name
     *
     * @return \CDatabase_Connection
     */
    public function connection($name = null) {
        list($database, $type) = $this->parseConnectionName($name);

        $name = $name ?: $database;

        // If we haven't created this connection, we'll create it based on the config
        // provided in the application. Once we've created the connections we will
        // set the "fetch mode" for PDO which determines the query return types.

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->configure(
                $this->makeConnection($database),
                $type
            );
            CEvent::dispatcher()->dispatch(new CDatabase_Event_ConnectionCreated($this->connections[$name]));
        }

        return $this->connections[$name];
    }

    /**
     * Alias for connection.
     *
     * @param string $name
     *
     * @return CDatabase_Connection
     */
    public function getConnection($name = null) {
        return $this->connection($name);
    }

    /**
     * Parse the connection into an array of the name and read / write type.
     *
     * @param string $name
     *
     * @return array
     */
    protected function parseConnectionName($name) {
        $name = $name ?: $this->getDefaultConnection();

        return cstr::endsWith($name, ['::read', '::write'])
            ? explode('::', $name, 2) : [$name, null];
    }

    /**
     * Make the database connection instance.
     *
     * @param string $name
     *
     * @return \CDatabase_Connection
     */
    protected function makeConnection($name) {
        $config = $this->configuration($name);

        // First we will check by the connection name to see if an extension has been
        // registered specifically for that connection. If it has we will call the
        // Closure and pass it the config allowing it to resolve the connection.
        if (isset($this->extensions[$name])) {
            return call_user_func($this->extensions[$name], $config, $name);
        }

        // Next we will check to see if an extension has been registered for a driver
        // and will call the Closure if so, which allows us to have a more generic
        // resolver for the drivers themselves which applies to all connections.
        if (isset($this->extensions[$driver = $config['driver']])) {
            return call_user_func($this->extensions[$driver], $config, $name);
        }

        if ($driver == 'mongodb') {
            return $this->createMongoDBConnection($config);
        }

        return CDatabase_ConnectionFactory::instance()->make($config, $name);
    }

    /**
     * Get the configuration for a connection.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function configuration($name) {
        $name = $name ?: $this->getDefaultConnection();
        $connections = carr::get($this->config, 'connections');
        if ($name == 'default') {
            $defaultValue = carr::get($this->config, 'default');
            if (is_string($defaultValue)) {
                $name = $defaultValue;
            }
        }
        $config = carr::get($connections, $name, carr::get($this->config, $name));

        if (is_null($config)) {
            throw new InvalidArgumentException("Database connection [{$name}] not configured.");
        }

        $config = (new CDatabase_ConfigurationUrlParser())
            ->parseConfiguration($config);
        $config = CDatabase_Config::flattenFormat($config);

        return $config;
    }

    /**
     * Prepare the database connection instance.
     *
     * @param CDatabase_Connection $connection
     * @param string               $type
     *
     * @return CDatabase_Connection
     */
    protected function configure(CDatabase_Connection $connection, $type) {
        $connection = $this->setPdoForType($connection, $type)->setReadWriteType($type);
        // Here we'll set a reconnector callback. This reconnector can be any callable
        // so we will set a Closure to reconnect from this manager with the name of
        // the connection, which will allow us to reconnect from the connections.
        $connection->setEventDispatcher(CEvent::dispatcher());
        $connection->setTransactionManager(CDatabase::transactionManager());
        $connection->setReconnector($this->reconnector);

        $this->registerConfiguredDoctrineTypes($connection);

        return $connection;
    }

    /**
     * Prepare the read / write mode for database connection instance.
     *
     * @param \CDatabase_Connection $connection
     * @param null|string           $type
     *
     * @return \CDatabase_Connection
     */
    protected function setPdoForType(CDatabase_Connection $connection, $type = null) {
        if ($type === 'read') {
            $connection->setPdo($connection->getReadPdo());
        } elseif ($type === 'write') {
            $connection->setReadPdo($connection->getPdo());
        }

        return $connection;
    }

    /**
     * Register custom Doctrine types with the connection.
     *
     * @param \CDatabase_Connection $connection
     *
     * @return void
     */
    protected function registerConfiguredDoctrineTypes(CDatabase_Connection $connection) {
        foreach (CF::config('database.dbal.types', []) as $name => $class) {
            $this->registerDoctrineType($class, $name, $name);
        }

        foreach ($this->doctrineTypes as $name => [$type, $class]) {
            $connection->registerDoctrineType($class, $name, $type);
        }
    }

    /**
     * Register a custom Doctrine type.
     *
     * @param string $class
     * @param string $name
     * @param string $type
     *
     * @throws \Doctrine\DBAL\Exception
     * @throws \RuntimeException
     *
     * @return void
     */
    public function registerDoctrineType(string $class, string $name, string $type): void {
        if (!class_exists('Doctrine\DBAL\Connection')) {
            throw new RuntimeException(
                'Registering a custom Doctrine type requires Doctrine DBAL (doctrine/dbal).'
            );
        }

        if (!Type::hasType($name)) {
            Type::addType($name, $class);
        }

        $this->doctrineTypes[$name] = [$type, $class];
    }

    /**
     * Disconnect from the given database and remove from local cache.
     *
     * @param null|string $name
     *
     * @return void
     */
    public function purge($name = null) {
        $name = $name ?: $this->getDefaultConnection();

        $this->disconnect($name);

        unset($this->connections[$name]);
    }

    /**
     * Disconnect from the given database.
     *
     * @param null|string $name
     *
     * @return void
     */
    public function disconnect($name = null) {
        if (isset($this->connections[$name = $name ?: $this->getDefaultConnection()])) {
            $this->connections[$name]->disconnect();
        }
    }

    /**
     * Reconnect to the given database.
     *
     * @param null|string $name
     *
     * @return \CDatabase_Connection
     */
    public function reconnect($name = null) {
        $this->disconnect($name = $name ?: $this->getDefaultConnection());

        if (!isset($this->connections[$name])) {
            return $this->connection($name);
        }

        return $this->refreshPdoConnections($name);
    }

    /**
     * Set the default database connection for the callback execution.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return mixed
     */
    public function usingConnection($name, callable $callback) {
        $previousName = $this->getDefaultConnection();

        $this->setDefaultConnection($name);

        return c::tap($callback(), function () use ($previousName) {
            $this->setDefaultConnection($previousName);
        });
    }

    /**
     * Refresh the PDO connections on a given connection.
     *
     * @param string $name
     *
     * @return \CDatabase_Connection
     */
    protected function refreshPdoConnections($name) {
        list($database, $type) = $this->parseConnectionName($name);

        $fresh = $this->configure(
            $this->makeConnection($database),
            $type
        );

        return $this->connections[$name]
            ->setPdo($fresh->getRawPdo())
            ->setReadPdo($fresh->getRawReadPdo());
    }

    /**
     * Get the default connection name.
     *
     * @return string
     */
    public function getDefaultConnection() {
        return $this->defaultConnection;
    }

    /**
     * Set the default connection name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultConnection($name) {
        $this->defaultConnection = $name;
    }

    /**
     * Get all of the support drivers.
     *
     * @return array
     */
    public function supportedDrivers() {
        return ['mysql', 'pgsql', 'sqlite', 'sqlsrv'];
    }

    /**
     * Get all of the drivers that are actually available.
     *
     * @return array
     */
    public function availableDrivers() {
        return array_intersect(
            $this->supportedDrivers(),
            str_replace('dblib', 'sqlsrv', PDO::getAvailableDrivers())
        );
    }

    /**
     * Register an extension connection resolver.
     *
     * @param string   $name
     * @param callable $resolver
     *
     * @return void
     */
    public function extend($name, callable $resolver) {
        $this->extensions[$name] = $resolver;
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function getConnections() {
        return $this->connections;
    }

    /**
     * Set the database reconnector callback.
     *
     * @param callable $reconnector
     *
     * @return void
     */
    public function setReconnector(callable $reconnector) {
        $this->reconnector = $reconnector;
    }

    /**
     * Register a connection with the manager.
     *
     * @param array  $config
     * @param string $name
     *
     * @return void
     */
    public function addConnection(array $config, $name = 'default') {
        $connections = carr::get($this->config, 'connections');

        $connections[$name] = $config;

        carr::set($this->config, 'connections', $connections);
    }

    /**
     * Register a connection with the manager.
     *
     * @param array  $config
     * @param string $name
     *
     * @return void
     */
    public function addRedisConnection(array $config, $name = 'default') {
        $redisConnections = carr::get($this->config, 'redis');

        //blacklist name for client and options
        $invalidNames = ['client', ' options'];
        if (in_array($name, $invalidNames)) {
            throw new Exception(sprintf('invalid name for name %s when add redis connection', $name));
        }
        $redisConnections[$name] = $config;

        carr::set($this->config, 'redis', $redisConnections);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->connection()->$method(...$parameters);
    }

    public function createMongoDBConnection($config) {
        return new CDatabase_Connection_MongoDBConnection($config);
    }
}

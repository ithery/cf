<?php

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
     * The custom connection resolvers.
     *
     * @var array<string, callable>
     */
    protected $extensions = [];

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
        $this->defaultConnection = 'default';
        $this->reconnector = function ($connection) {
            $this->reconnect($connection->getNameWithReadWriteType());
        };
    }

    /**
     * Get a database connection instance.
     *
     * @param null|string $name
     *
     * @return \CDatabase_Connection
     */
    public function connection($name = null) {
        if (is_array($name)) {
            $config = $name;
            $name = carr::hash($config);

            return CDatabase_ConnectionFactory::instance()->make($config, $name);
        }
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
        }

        return $this->connections[$name];
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
        $config = CDatabase_Config::resolve($name);

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
        $connection = $this->setDriverForType($connection, $type)->setReadWriteType($type);

        // Here we'll set a reconnector callback. This reconnector can be any callable
        // so we will set a Closure to reconnect from this manager with the name of
        // the connection, which will allow us to reconnect from the connections.
        $connection->setReconnector($this->reconnector);

        //$this->registerConfiguredDoctrineTypes($connection);

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
    protected function setDriverForType(CDatabase_Connection $connection, $type = null) {
        if ($type === 'read') {
            $connection->setDriver($connection->getReadDriver());
        } elseif ($type === 'write') {
            $connection->setReadDriver($connection->getDriver());
        }

        return $connection;
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
            ->setDriver($fresh->getRawDriver())
            ->setReadDriver($fresh->getRawReadDriver());
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
}

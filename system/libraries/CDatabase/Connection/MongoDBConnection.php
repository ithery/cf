<?php

use MongoDB\Client;

use MongoDB\Database;
use Composer\InstalledVersions;

class CDatabase_Connection_MongoDBConnection extends CDatabase_Connection {
    use CDatabase_MongoDB_Trait_ManagesTransactionsTrait;

    /**
     * The MongoDB database handler.
     *
     * @var Database
     */
    protected $db;

    /**
     * The MongoDB connection handler.
     *
     * @var Client
     */
    protected $connection;

    private static ?string $version = null;

    /**
     * Create a new database connection instance.
     *
     * @param array $config
     */
    public function __construct(array $config) {
        $this->config = $config;

        // Build the connection string
        $dsn = $this->getDsn($config);

        // You can pass options directly to the MongoDB constructor
        $options = carr::get($config, 'options', []);

        // Create the connection
        $this->connection = $this->createConnection($dsn, $config, $options);

        // Get default database name
        $default_db = $this->getDefaultDatabaseName($dsn, $config);

        // Select database
        $this->db = $this->connection->selectDatabase($default_db);

        $this->useDefaultPostProcessor();

        $this->useDefaultSchemaGrammar();

        $this->useDefaultQueryGrammar();

        $this->events = CEvent::dispatcher();
    }

    /**
     * Begin a fluent query against a database collection.
     *
     * @param string $collection
     *
     * @return CDatabase_Query_Builder_MongoDBBuilder
     */
    public function collection($collection) {
        $query = new CDatabase_Query_Builder_MongoDBBuilder($this, $this->getPostProcessor());

        return $query->from($collection);
    }

    /**
     * Begin a fluent query against a database collection.
     *
     * @param string      $table
     * @param null|string $as
     *
     * @return CDatabase_Query_Builder_MongoDBBuilder
     */
    public function table($table, $as = null) {
        return $this->collection($table);
    }

    /**
     * Get a MongoDB collection.
     *
     * @param string $name
     *
     * @return CDatabase_MongoDB_Collection
     */
    public function getCollection($name) {
        return new CDatabase_MongoDB_Collection($this, $this->db->selectCollection($name));
    }

    /**
     * @inheritdoc
     */
    public function getSchemaBuilder() {
        return new CDatabase_Schema_Builder_MongoDBBuilder($this);
    }

    /**
     * Get the MongoDB database object.
     *
     * @return Database
     */
    public function getMongoDB() {
        return $this->db;
    }

    /**
     * Return MongoDB object.
     *
     * @return Client
     */
    public function getMongoClient() {
        return $this->connection;
    }

    /**
     * @inheritdoc
     */
    public function getDatabaseName() {
        return $this->getMongoDB()->getDatabaseName();
    }

    /**
     * Get the name of the default database based on db config or try to detect it from dsn.
     *
     * @param string $dsn
     * @param array  $config
     *
     * @throws InvalidArgumentException
     *
     * @return string
     */
    protected function getDefaultDatabaseName(string $dsn, array $config): string {
        if (empty($config['database'])) {
            if (preg_match('/^mongodb(?:[+]srv)?:\\/\\/.+\\/([^?&]+)/s', $dsn, $matches)) {
                $config['database'] = $matches[1];
            } else {
                throw new InvalidArgumentException('Database is not properly configured.');
            }
        }

        return $config['database'];
    }

    /**
     * Create a new MongoDB connection.
     *
     * @param string $dsn
     * @param array  $config
     * @param array  $options
     *
     * @return Client
     */
    protected function createConnection($dsn, array $config, array $options): Client {
        // By default driver options is an empty array.
        $driverOptions = [];

        if (isset($config['driver_options']) && is_array($config['driver_options'])) {
            $driverOptions = $config['driver_options'];
        }

        $driverOptions['driver'] = [
            'name' => 'laravel-mongodb',
            'version' => self::getVersion(),
        ];

        // Check if the credentials are not already set in the options
        if (!isset($options['username']) && !empty($config['username'])) {
            $options['username'] = $config['username'];
        }
        if (!isset($options['password']) && !empty($config['password'])) {
            $options['password'] = $config['password'];
        }

        return new Client($dsn, $options, $driverOptions);
    }

    /**
     * @inheritdoc
     */
    public function disconnect() {
        unset($this->connection);
    }

    /**
     * Determine if the given configuration array has a dsn string.
     *
     * @param array $config
     *
     * @return bool
     */
    protected function hasDsnString(array $config) {
        return isset($config['dsn']) && !empty($config['dsn']);
    }

    /**
     * Get the DSN string form configuration.
     *
     * @param array $config
     *
     * @return string
     */
    protected function getDsnString(array $config): string {
        return $config['dsn'];
    }

    /**
     * Get the DSN string for a host / port configuration.
     *
     * @param array $config
     *
     * @return string
     */
    protected function getHostDsn(array $config): string {
        // Treat host option as array of hosts
        $hosts = is_array($config['host']) ? $config['host'] : [$config['host']];

        foreach ($hosts as &$host) {
            // Check if we need to add a port to the host
            if (strpos($host, ':') === false && !empty($config['port'])) {
                $host = $host . ':' . $config['port'];
            }
        }

        // Check if we want to authenticate against a specific database.
        $auth_database = isset($config['options']) && !empty($config['options']['database']) ? $config['options']['database'] : null;

        return 'mongodb://' . implode(',', $hosts) . ($auth_database ? '/' . $auth_database : '');
    }

    /**
     * Create a DSN string from a configuration.
     *
     * @param array $config
     *
     * @return string
     */
    protected function getDsn(array $config): string {
        return $this->hasDsnString($config)
            ? $this->getDsnString($config)
            : $this->getHostDsn($config);
    }

    /**
     * @inheritdoc
     */
    public function getElapsedTime($start) {
        return parent::getElapsedTime($start);
    }

    /**
     * @inheritdoc
     */
    public function getDriverName() {
        return 'mongodb';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultPostProcessor() {
        return new CDatabase_Query_Processor_MongoDBProcessor();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultQueryGrammar() {
        return new CDatabase_Query_Grammar_MongoDBGrammar();
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultSchemaGrammar() {
        return new CDatabase_Schema_Grammar_MongoDBGrammar();
    }

    /**
     * Set database.
     *
     * @param \MongoDB\Database $db
     */
    public function setDatabase(Database $db) {
        $this->db = $db;
    }

    /**
     * Dynamically pass methods to the connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->db->$method(...$parameters);
    }

    private static function getVersion(): string {
        return CF::version();
    }
}

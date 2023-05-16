<?php

defined('SYSPATH') or die('No direct access allowed.');

use Carbon\Carbon;

class CDatabase {
    use CTrait_Compat_Database;
    use CDatabase_Trait_DetectDeadlock;
    use CDatabase_Trait_DetectLostConnection;
    use CDatabase_Trait_DetectConcurrencyErrors;
    use CDatabase_Trait_ManageTransaction;

    /**
     * Database instances.
     *
     * @var array
     */
    public static $instances = [];

    /**
     * Global benchmark.
     *
     * @var array
     */
    public static $benchmarks = [];

    public $domain;

    public $name;

    protected $isPdo = false;

    protected $isBenchmarkQuery;

    /**
     * Default Database.
     *
     * @var string
     */
    protected static $defaultConnection = 'default';

    /**
     * Instance of schema manager.
     *
     * @var CDatabase_Schema_Manager
     */
    protected $schemaManager;

    /**
     * @var CDatabase_Platform
     */
    protected $platform;

    /**
     * @var CDatabase_Driver_Mysqli
     */
    protected $driver;

    /**
     * @var string
     */
    protected $driverName;

    /**
     * @var string
     */
    protected $driverClass;

    /**
     * @var CDatabase_Configuration
     */
    protected $configuration;

    /**
     * @var array
     */
    protected $config = [
        'benchmark' => true,
        'persistent' => false,
        'connection' => '',
        'character_set' => 'utf8',
        'table_prefix' => '',
        'object' => true,
        'cache' => false,
        'escape' => true,
    ];

    protected $link;

    protected $last_query = '';

    protected $queryLog = [];

    // Stack of queries for push/pop
    protected $query_history = [];

    /**
     * The number of active transactions.
     *
     * @var int
     */
    protected $transactions = 0;

    /**
     * The event dispatcher instance.
     *
     * @var CEvent_Dispatcher
     */
    protected $events;

    /**
     * The query grammar implementation.
     *
     * @var CDatabase_Query_Grammar
     */
    protected $queryGrammar;

    /**
     * The query post processor implementation.
     *
     * @var CDatabase_Query_Grammar_Processor
     */
    protected $postProcessor;

    /**
     * @var CDatabase_TransactionManager
     */
    protected $transactionManager;

    /**
     * Sets up the database configuration, loads the CDatabase_Driver.
     *
     * @param mixed      $config
     * @param null|mixed $domain
     *
     * @throws CDatabase_Exception
     */
    public function __construct($config = [], $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        $loadConfig = true;

        if (!empty($config)) {
            if (is_array($config) && count($config) > 0) {
                if (!array_key_exists('connection', $config)) {
                    $config = ['connection' => $config];
                    $loadConfig = false;
                } else {
                    $loadConfig = false;
                }
            }
            if (is_string($config)) {
                if (strpos($config, '://') !== false) {
                    $config = ['connection' => $config];
                    $loadConfig = false;
                }
            }
        }
        $configName = '';
        if ($loadConfig) {
            $found = false;
            $configName = static::$defaultConnection;
            if (is_string($config)) {
                $configName = $config;
            }
            $config = $this->resolveConfig($config);

            if (is_array($config)) {
                $found = true;
            }

            if ($found == false) {
                throw new Exception('Config ' . $configName . ' Not Found');
            }
        }

        $this->name = $configName;
        // Merge the default config with the passed config
        $this->config = array_merge($this->config, $config);
        $this->isBenchmarkQuery = carr::get($this->config, 'benchmark', false);

        if (is_string($this->config['connection'])) {
            // Make sure the connection is valid
            if (strpos($this->config['connection'], '://') === false) {
                throw new CDatabase_Exception('The DSN you supplied is not valid: :dsn', [':dsn' => $this->config['connection']]);
            }
            // Parse the DSN, creating an array to hold the connection parameters
            $db = [
                'type' => false,
                'user' => false,
                'pass' => false,
                'host' => false,
                'port' => false,
                'socket' => false,
                'database' => false
            ];

            // Get the protocol and arguments
            list($db['type'], $connection) = explode('://', $this->config['connection'], 2);

            if (strpos($connection, '@') !== false) {
                // Get the username and password
                list($db['pass'], $connection) = explode('@', $connection, 2);
                // Check if a password is supplied
                $logindata = explode(':', $db['pass'], 2);
                $db['pass'] = (count($logindata) > 1) ? $logindata[1] : '';
                $db['user'] = $logindata[0];

                // Prepare for finding the database
                $connection = explode('/', $connection);

                // Find the database name
                $db['database'] = array_pop($connection);

                // Reset connection string
                $connection = implode('/', $connection);

                // Find the socket
                if (preg_match('/^unix\([^)]++\)/', $connection)) {
                    // This one is a little hairy: we explode based on the end of
                    // the socket, removing the 'unix(' from the connection string
                    list($db['socket'], $connection) = explode(')', substr($connection, 5), 2);
                } elseif (strpos($connection, ':') !== false) {
                    // Fetch the host and port name
                    list($db['host'], $db['port']) = explode(':', $connection, 2);
                } else {
                    $db['host'] = $connection;
                }
            } else {
                // File connection
                $connection = explode('/', $connection);

                // Find database file name
                $db['database'] = array_pop($connection);

                // Find database directory name
                $db['socket'] = implode('/', $connection) . '/';
            }

            // Reset the connection array to the database config
            $this->config['connection'] = $db;
        }
        // Set driver name

        $connectionType = $this->config['connection']['type'];
        $this->isPdo = carr::get($this->config, 'connection.pdo');
        $pdoDriverMap = [
            'sqlite' => CDatabase_Driver_PDO_Sqlite::class
        ];
        $nativeDriverMap = [
            'mysqli' => CDatabase_Driver_Mysqli::class,
            'sqlsrv' => CDatabase_Driver_Sqlsrv::class,
            'mongodb' => CDatabase_Driver_MongoDB::class,
        ];
        $driverMap = $this->isPdo ? $pdoDriverMap : $nativeDriverMap;

        $this->driverName = c::classBasename(carr::get($driverMap, $connectionType, ucfirst($connectionType)));
        $this->driverClass = carr::get($driverMap, $connectionType, 'CDatabase_Driver_' . $this->driverName);

        try {
            // Validation of the driver
            $class = new ReflectionClass($this->driverClass);
            // Initialize the driver
            $this->driver = $class->newInstance($this, $this->config);
        } catch (ReflectionException $ex) {
            throw new CDatabase_Exception('The :driver driver for the :class library could not be found', [':driver' => $this->driverClass, ':class' => get_class($this)]);
        }

        $this->events = CEvent::dispatcher();

        $this->configuration = new CDatabase_Configuration();
        // Validate the driver
        if (!($this->driver instanceof CDatabase_Driver)) {
            throw new CDatabase_Exception('The :driver driver for the :class library must implement the :interface interface', [':driver' => $this->driverClass, ':class' => get_class($this), ':interface' => 'CDatabase_Driver']);
        }

        $this->transactionManager = new CDatabase_TransactionManager();
    }

    public function __destruct() {
        $this->rollback();

        try {
            if ($this->driver != null) {
                $this->driver->close();
            }
        } catch (Exception $ex) {
        }
    }

    public function config() {
        return $this->config;
    }

    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    /**
     * Returns a singleton instance of Database.
     *
     * @param null|mixed $name
     * @param null|mixed $config
     * @param null|mixed $domain
     *
     * @return CDatabase
     */
    public static function &instance($name = null, $config = null, $domain = null) {
        if ($name == null) {
            $name = static::$defaultConnection;
        }

        if (strlen($domain) == 0) {
            //get current domain
            $domain = CF::domain();
        }
        if ($name == null) {
            $name = 'default';
        }

        if (!isset(CDatabase::$instances[$domain])) {
            CDatabase::$instances[$domain] = [];
        }
        if (!isset(CDatabase::$instances[$domain][$name])) {
            // Create a new instance

            CDatabase::$instances[$domain][$name] = new CDatabase($config === null ? $name : $config, $domain);
        }

        return CDatabase::$instances[$domain][$name];
    }

    /**
     * Returns the name of a given database instance.
     *
     * @param   CDatabase  instance of CDatabase
     * @param null|mixed $domain
     *
     * @return string
     */
    public static function instanceName(CDatabase $db, $domain = null) {
        if (strlen($domain) == 0) {
            //get current domain
            $domain = CF::domain();
        }

        return array_search($db, CDatabase::$instances[$domain], true);
    }

    /**
     * @param type $config
     */
    public function resolveConfig($config) {
        if (!is_array($config)) {
            $config = CF::config('database.' . $config);
            if (is_string($config)) {
                $config = $this->resolveConfig($config);
            }
        }

        return $config;
    }

    /**
     * Simple connect method to get the database queries up and running.
     *
     * @return void
     */
    public function connect() {
        // A link can be a resource or an object
        if (!is_resource($this->link) and !is_object($this->link)) {
            $this->link = $this->driver->connect();
            if (!is_resource($this->link) and !is_object($this->link)) {
                throw new CDatabase_Exception('There was an error connecting to the database: :error', [':error' => $this->driver->showError()]);
            }
            // Clear password after successful connect
            $this->config['connection']['pass'] = null;
        }
    }

    public function close() {
        $this->driver->close();
        $this->link = null;
    }

    /**
     * Runs a query into the driver and returns the result.
     *
     * @param string $sql        SQL query to execute
     * @param array  $bindings
     * @param bool   $useReadPdo
     *
     * @return CDatabase_Result
     */
    public function query($sql = '', $bindings = [], $useReadPdo = true) {
        if ($sql == '') {
            return false;
        }

        // No link? Connect!
        $this->link or $this->connect();

        // Start the benchmark
        $start = microtime(true);

        // Compile binds if needed

        $sql = $this->compileBinds($sql, $bindings);

        // Fetch the result
        $result = $this->driver->query($this->last_query = $sql);

        // Stop the benchmark
        $elapsedTime = $this->getElapsedTime($start);

        if ($this->isBenchmarkQuery()) {
            $this->benchmarkQuery($sql, $elapsedTime, count($result));
            // Benchmark the query
            //CDatabase::$benchmarks[] = array('query' => $sql, 'time' => $elapsedTime, 'rows' => count($result), 'caller' => cdbg::callerInfo());
        }

        // Once we have run the query we will calculate the time that it took to run and
        // then log the query, bindings, and execution time so we will report them on
        // the event that the developer needs them. We'll log time in milliseconds.
        $this->logQuery($sql, $bindings, $elapsedTime, $result->count());

        return $result;
    }

    /**
     * Get the elapsed time since a given starting point.
     *
     * @param int $start
     *
     * @return float
     */
    protected function getElapsedTime($start) {
        return round((microtime(true) - $start) * 1000, 2);
    }

    /**
     * Prepare the query bindings for execution.
     *
     * @param array $bindings
     *
     * @return array
     */
    public function prepareBindings(array $bindings) {
        $grammar = $this->getQueryGrammar();

        foreach ($bindings as $key => $value) {
            // We need to transform all instances of DateTimeInterface into the actual
            // date string. Each query grammar maintains its own date string format
            // so we'll just ask the grammar for the format to get from the date.
            if ($value instanceof DateTimeInterface) {
                $bindings[$key] = $value->format($grammar->getDateFormat());
            } elseif (is_bool($value)) {
                $bindings[$key] = (int) $value;
            }
        }

        return $bindings;
    }

    /**
     * Compiles an insert string and runs the query.
     *
     * @param string $table table name
     * @param array  $set   array of key/value pairs to insert
     *
     * @return CDatabase_Result Query result
     */
    public function insert($table, $set) {
        return $this->table($table)->insert($set);
    }

    /**
     * Compiles an update string and runs the query.
     *
     * @param string $table table name
     * @param array  $set   associative array of update values
     * @param array  $where where clause
     *
     * @return CDatabase_Result Query result
     */
    public function update($table = '', $set = null, $where = null) {
        return $this->table($table)->where($where)->update($set);
    }

    /**
     * Compiles a delete string and runs the query.
     *
     * @param string $table table name
     * @param array  $where where clause
     *
     * @return CDatabase_Result Query result
     */
    public function delete($table = '', $where = []) {
        if ($where == null || count($where) < 1) {
            throw new CDatabase_Exception('You must set a WHERE clause for your query');
        }
        $builder = $this->table($table);

        return $builder->where($where)->delete();
    }

    /**
     * Returns the last query run.
     *
     * @return string SQL
     */
    public function lastQuery() {
        return $this->last_query;
    }

    /**
     * Set the last query run.
     *
     * @param mixed $sql
     *
     * @return string SQL
     */
    public function setLastQuery($sql) {
        return $this->last_query = $sql;
    }

    /**
     * Lists all the tables in the current database.
     *
     * @return array
     */
    public function listTables() {
        $this->link or $this->connect();

        return $this->driver->listTables();
    }

    /**
     * See if a table exists in the database.
     *
     * @param string $table_name table name
     * @param bool   $prefix     True to attach table prefix
     *
     * @return bool
     */
    public function tableExists($table_name, $prefix = true) {
        if ($prefix) {
            return in_array($this->config['table_prefix'] . $table_name, $this->listTables());
        }

        return in_array($table_name, $this->listTables());
    }

    /**
     * Combine a SQL statement with the bind values. Used for safe queries.
     *
     * @param string $sql   query to bind to the values
     * @param array  $binds array of values to bind to the query
     *
     * @return string
     */
    public function compileBinds($sql, $binds) {
        foreach ((array) $binds as $val) {
            // If the SQL contains no more bind marks ("?"), we're done.
            if (($next_bind_pos = strpos($sql, '?')) === false) {
                break;
            }
            if ($val instanceof Carbon) {
                $val = (string) $val;
            }
            // Properly escape the bind value.
            $val = $this->driver->escape($val);

            // Temporarily replace possible bind marks ("?"), in the bind value itself, with a placeholder.
            $val = str_replace('?', '{%B%}', $val);

            // Replace the first bind mark ("?") with its corresponding value.
            $sql = substr($sql, 0, $next_bind_pos) . $val . substr($sql, $next_bind_pos + 1);
        }

        // Restore placeholders.
        return str_replace('{%B%}', '?', $sql);
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param string $table table name
     *
     * @return array
     */
    public function fieldData($table = '') {
        $this->link or $this->connect();

        return $this->driver->fieldData($this->config['table_prefix'] . $table);
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param string $table table name
     *
     * @return array
     */
    public function listFields($table = '') {
        $this->link or $this->connect();

        return $this->driver->listFields($this->config['table_prefix'] . $table);
    }

    /**
     * Escapes a value for a query.
     *
     * @param mixed $value value to escape
     *
     * @return string
     */
    public function escape($value) {
        return $this->driver->escape($value);
    }

    /**
     * Escapes a string for a query.
     *
     * @param string $str string to escape
     *
     * @return string
     */
    public function escapeStr($str) {
        return $this->driver->escapeStr($str);
    }

    /**
     * Escapes a table name for a query.
     *
     * @param string $table string to escape
     *
     * @return string
     */
    public function escapeTable($table) {
        return $this->driver->escapeTable($table);
    }

    /**
     * Escapes a column name for a query.
     *
     * @param string $table string to escape
     *
     * @return string
     */
    public function escapeColumn($table) {
        return $this->driver->escapeColumn($table);
    }

    /**
     * Count the number of records in the last query, without LIMIT or OFFSET applied.
     *
     * @return int
     */
    public function countLastQuery() {
        if ($sql = $this->lastQuery()) {
            if (stripos($sql, 'LIMIT') !== false) {
                // Remove LIMIT from the SQL
                $sql = preg_replace('/\sLIMIT\s+[^a-z]+/i', ' ', $sql);
            }

            if (stripos($sql, 'OFFSET') !== false) {
                // Remove OFFSET from the SQL
                $sql = preg_replace('/\sOFFSET\s+\d+/i', '', $sql);
            }

            // Get the total rows from the last query executed
            $result = $this->query(
                'SELECT COUNT(*) AS ' . $this->escapeColumn('total_rows') . ' '
                    . 'FROM (' . trim($sql) . ') AS ' . $this->escapeTable('counted_results')
            );

            // Return the total number of rows from the query
            return (int) $result->current()->total_rows;
        }

        return false;
    }

    public function escapeLike($str) {
        $str = $this->escapeStr($str);

        return $str;
    }

    public function driverName() {
        return $this->driverName;
    }

    /**
     * Get Query Builder from table.
     *
     * @param string $table
     *
     * @return CDatabase_Query_Builder
     */
    public function table($table) {
        $builderClass = $this->driverName == 'MongoDB' ? CDatabase_Query_Builder_MongoDBBuilder::class : CDatabase_Query_Builder::class;
        $builder = $this->driverName == 'MongoDB' ? new $builderClass($this, new CDatabase_Query_Processor_MongoDB()) : new $builderClass($this);
        /** @var CDatabase_Query_Builder $builder */
        return $builder->from($table);
    }

    /**
     * Reconnect to the database.
     *
     * @throws \LogicException
     *
     * @return void
     */
    public function reconnect() {
        $this->driver->close();
        $this->driver->connect();
    }

    /**
     * Register a database query listener with the connection.
     *
     * @param \Closure $callback
     *
     * @deprecated since 1.5 use CEvent::dispatcher()->listen(CDatabase_Event_OnQueryExecuted::class, function (CDatabase_Event_OnQueryExecuted $query) {});
     *
     * @return void
     */
    public function listenOnQueryExecuted(Closure $callback) {
        if (isset($this->events)) {
            $this->events->listen(CDatabase_Event_OnQueryExecuted::class, $callback);
        }
    }

    public function listen($event, Closure $callback) {
        if (isset($this->events)) {
            $this->events->listen($event, $callback);
        }
    }

    /**
     * Fire the given event if possible.
     *
     * @param mixed $event
     *
     * @return void
     */
    protected function dispatchEvent($event) {
        if (isset($this->events)) {
            $this->events->dispatch($event);
        }
    }

    /**
     * Get the event dispatcher used by the connection.
     *
     * @return CEvent
     */
    public function getEventDispatcher() {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance on the connection.
     *
     * @param CEvent $events
     *
     * @return void
     */
    public function setEventDispatcher(CEvent $events) {
        $this->events = $events;
    }

    public function unsetEventDispatcher() {
        $this->events = null;

        return $this;
    }

    /**
     * Get the query grammar used by the connection.
     *
     * @return CDatabase_Query_Grammar
     */
    public function getQueryGrammar() {
        if ($this->queryGrammar == null) {
            $driverName = $this->driverName();
            $grammarClass = 'CDatabase_Query_Grammar_' . $driverName;
            $this->queryGrammar = new $grammarClass();
        }

        return $this->queryGrammar;
    }

    public function getName() {
        return $this->name;
    }

    /**
     * Gets the SchemaManager that can be used to inspect or change the
     * database schema through the connection.
     *
     * @return CDatabase_Schema_Manager
     */
    public function getSchemaManager() {
        if (!$this->schemaManager) {
            $this->schemaManager = $this->driver->getSchemaManager($this);
        }

        return $this->schemaManager;
    }

    /**
     * Gets the DatabasePlatform for the connection.
     *
     * @throws CDatabase_Exception
     *
     * @return CDatabase_Platform
     */
    public function getDatabasePlatform() {
        if (null === $this->platform) {
            $this->detectDatabasePlatform();
        }

        return $this->platform;
    }

    /**
     * Detects and sets the database platform.
     *
     * Evaluates custom platform class and version in order to set the correct platform.
     *
     * @throws CDatabase_Exception if an invalid platform was specified for this connection
     */
    private function detectDatabasePlatform() {
        $version = $this->getDatabasePlatformVersion();

        if ($version !== null) {
            assert($this->driver instanceof CDatabase_Driver_VersionAwarePlatformInterface);

            $this->platform = $this->driver->createDatabasePlatformForVersion($version);
        } else {
            $this->platform = $this->driver->getDatabasePlatform();
        }

        $this->platform->setEventManager($this->events);
    }

    /**
     * Returns the version of the related platform if applicable.
     *
     * Returns null if either the driver is not capable to create version
     * specific platform instances, no explicit server version was specified
     * or the underlying driver connection cannot determine the platform
     * version without having to query it (performance reasons).
     *
     * @throws Exception
     *
     * @return null|string
     */
    private function getDatabasePlatformVersion() {
        // Driver does not support version specific platforms.

        if (!($this->driver instanceof CDatabase_Driver_VersionAwarePlatformInterface)) {
            return null;
        }

        // Explicit platform version requested (supersedes auto-detection).
        if (isset($this->config['serverVersion'])) {
            return $this->config['serverVersion'];
        }

        return $this->getServerVersion();
    }

    /**
     * Returns the database server version if the underlying driver supports it.
     *
     * @return null|string
     */
    private function getServerVersion() {
        // Automatic platform version detection.

        if ($this->driver instanceof CDatabase_Driver_ServerInfoAwareInterface && !$this->driver->requiresQueryForServerVersion()) {
            return $this->driver->getServerVersion();
        }

        // Unable to detect platform version.
        return null;
    }

    /**
     * Gets the Configuration used by the Connection.
     *
     * @return CDatabase_Configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * Gets the name of the database this Connection is connected to.
     *
     * @return string
     */
    public function getDatabase() {
        return $this->driver->getDatabase($this);
    }

    public function isLogQuery() {
        return carr::get($this->config, 'log', false);
    }

    public function enableBenchmark() {
        $this->isBenchmarkQuery = true;
    }

    public function disableBenchmark() {
        $this->isBenchmarkQuery = false;
    }

    public function isBenchmarkQuery() {
        return $this->isBenchmarkQuery;
    }

    public function benchmarkQuery($query, $time = null, $rowsCount = null) {
        if ($this->isBenchmarkQuery()) {
            // Benchmark the query
            static::$benchmarks[] = ['query' => $query, 'time' => $time, 'rows' => $rowsCount, 'caller' => cdbg::callerInfo()];
        }
    }

    /**
     * Log a query in the connection's query log.
     *
     * @param string     $query
     * @param array      $bindings
     * @param null|float $time
     * @param null|mixed $rowsCount
     *
     * @return void
     */
    public function logQuery($query, $bindings, $time = null, $rowsCount = null) {
        if ($this->driverName() == 'MongoDB') {
            if (is_array($query)) {
                $query = CDatabase_Helper_MongoDB::commandToString($query);
            }
        }
        $this->dispatchEvent(CDatabase_Event::createOnQueryExecutedEvent($query, $bindings, $time, $rowsCount, $this));

        if ($this->isLogQuery()) {
            $this->queryLog[] = compact('query', 'bindings', 'time');
        }
    }

    public function enableQueryLog() {
        $this->config['log'] = true;
    }

    public function getQueryLog() {
        return $this->queryLog;
    }

    /**
     * Get a new raw query expression.
     *
     * @param mixed $value
     *
     * @return CDatabase_Query_Expression
     */
    public static function raw($value) {
        return new CDatabase_Query_Expression($value);
    }

    /**
     * Get the name of the connected database.
     *
     * @return string
     */
    public function getDatabaseName() {
        return carr::get($this->config, 'connection.database');
    }

    public function getRow($query) {
        $r = $this->query($query);
        $result = null;
        if ($r->count() > 0) {
            $result = $r[0];
        }

        return $result;
    }

    public function getValue($query) {
        $r = $this->query($query);
        $result = $r->result(false);
        $res = [];
        $value = null;
        foreach ($result as $row) {
            foreach ($row as $k => $v) {
                $value = $v;

                break;
            }

            break;
        }

        return $value;
    }

    public function getArray($query) {
        $r = $this->query($query);
        $result = $r->result(false);
        $res = [];
        foreach ($result as $row) {
            $cnt = 0;
            $arr_val = '';
            foreach ($row as $k => $v) {
                if ($cnt == 0) {
                    $arr_val = $v;
                }
                $cnt++;
                if ($cnt > 0) {
                    break;
                }
            }
            $res[] = $arr_val;
        }

        return $res;
    }

    public function getList($query) {
        $r = $this->query($query);
        $result = $r->result(false);
        $res = [];
        foreach ($result as $row) {
            $cnt = 0;
            $arr_key = '';
            $arr_val = '';
            foreach ($row as $k => $v) {
                if ($cnt == 0) {
                    $arr_key = $v;
                }
                if ($cnt == 1) {
                    $arr_val = $v;
                }
                $cnt++;
                if ($cnt > 1) {
                    break;
                }
            }
            $res[$arr_key] = $arr_val;
        }

        return $res;
    }

    /**
     * Get a new query builder instance.
     *
     * @return CDatabase_Query_Builder
     */
    public function createQueryBuilder() {
        return $this->newQuery();
    }

    /**
     * Fire an event for this connection.
     *
     * @param string $event
     *
     * @return null|array
     */
    protected function fireConnectionEvent($event) {
        if (!isset($this->events)) {
            return;
        }
        switch ($event) {
            case 'beganTransaction':
                return $this->events->dispatch(new CDatabase_Event_Transaction_Beginning($this));
            case 'committed':
                return $this->events->dispatch(new CDatabase_Event_Transaction_Committed($this));
            case 'rollingBack':
                return $this->events->dispatch(new CDatabase_Event_Transaction_RolledBack($this));
        }
    }

    /**
     * Get a new query builder instance.
     *
     * @return CDatabase_Query_Builder
     */
    public function newQuery() {
        return new CDatabase_Query_Builder($this);
    }

    /**
     * Get the query post processor used by the connection.
     *
     * @return CDatabase_Query_Processor
     */
    public function getPostProcessor() {
        if ($this->postProcessor == null) {
            $driverName = $this->driverName();
            $processorClass = 'CDatabase_Query_Processor_' . $driverName;
            $this->postProcessor = new $processorClass();
        }

        return $this->postProcessor;
    }

    public function driver() {
        return $this->driver;
    }

    public function ping() {
        return $this->driver->ping();
    }

    /**
     * Prepares and executes an SQL query and returns the result as an associative array.
     *
     * @param string $sql    the SQL query
     * @param array  $params the query parameters
     *
     * @return array
     */
    public function fetchAll($sql, array $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function getTablePrefix() {
        return carr::get($this->config, 'table_prefix');
    }

    /**
     * Get a Doctrine Schema Column instance.
     *
     * @param string $table
     * @param string $column
     *
     * @return \CDatabase_Schema_Column
     */
    public function getColumn($table, $column) {
        $schema = $this->getSchemaManager();

        return $schema->listTableDetails($table)->getColumn($column);
    }

    public static function resetBenchmarks() {
        static::$benchmarks = [];
    }

    public static function getBenchmarks() {
        return static::$benchmarks;
    }

    public static function manager() {
        return CDatabase_Manager::instance();
    }

    /**
     * Reconnect to the database if a PDO connection is missing.
     *
     * @return void
     */
    protected function reconnectIfMissingConnection() {
        $this->driver->reconnect();
    }

    /**
     * @return CDatabase_TransactionManager
     */
    public function getTransactionManager() {
        return $this->transactionManager;
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \CDatabase_Schema_Builder
     */
    public function getSchemaBuilder() {
        return new CDatabase_Schema_Builder($this);
    }

    /**
     * Get a schema builder instance for the connection.
     *
     * @return \CDatabase_Schema_Grammar
     */
    public function getSchemaGrammar() {
        if ($this->queryGrammar == null) {
            $driverName = $this->driverName();
            $grammarClass = 'CDatabase_Schema_Grammar_' . $driverName;
            $this->queryGrammar = new $grammarClass();
        }

        return $this->queryGrammar;
    }
}

// End Database Class

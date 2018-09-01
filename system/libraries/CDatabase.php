<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CDatabase {

    // Database instances
    public static $instances = array();
    // Global benchmark
    public static $benchmarks = array();
    public $domain;
    public $name;
    public $config_file;

    /**
     *
     * @var CDatabase_Schema_Manager
     */
    protected $schemaManager;

    /**
     *
     * @var CDatabase_Platform
     */
    protected $platform;

    /**
     *
     * @var CDatabase_Configuration
     */
    protected $configuration;
    // Configuration
    protected $config = array(
        'benchmark' => TRUE,
        'persistent' => FALSE,
        'connection' => '',
        'character_set' => 'utf8',
        'table_prefix' => '',
        'object' => TRUE,
        'cache' => FALSE,
        'escape' => TRUE,
    );
    // Database driver object
    protected $driver;
    protected $driver_name;
    protected $link;
    // Un-compiled parts of the SQL query
    protected $select = array();
    protected $set = array();
    protected $from = array();
    protected $join = array();
    protected $where = array();
    protected $orderby = array();
    protected $order = array();
    protected $groupby = array();
    protected $having = array();
    protected $distinct = FALSE;
    protected $limit = FALSE;
    protected $offset = FALSE;
    protected $last_query = '';
    // Stack of queries for push/pop
    protected $query_history = array();

    /**
     * The event dispatcher instance.
     *
     * @var CContracts_Events_Dispatcher
     */
    protected $events;

    /**
     * The query grammar implementation.
     *
     * @var CDatabase_Query_Grammar
     */
    protected $queryGrammar;

    public function config() {
        return $this->config;
    }

    /**
     * Returns a singleton instance of Database.
     *
     * @param   mixed   configuration array or DSN
     * @return  CDatabase
     */
    public static function &instance($domain = null, $name = 'default', $config = NULL) {
        if (strlen($domain) == 0) {
            //get current domain
            $domain = CF::domain();
        }
        if (!isset(CDatabase::$instances[$domain])) {
            CDatabase::$instances[$domain] = array();
        }
        if (!isset(CDatabase::$instances[$domain][$name])) {
            // Create a new instance
            CDatabase::$instances[$domain][$name] = new CDatabase($config === NULL ? $name : $config, $domain);
        }

        return CDatabase::$instances[$domain][$name];
    }

    /**
     * Returns the name of a given database instance.
     *
     * @param   CDatabase  instance of CDatabase
     * @return  string
     */
    public static function instanceName(CDatabase $db, $domain = null) {
        if (strlen($domain) == 0) {
            //get current domain
            $domain = CF::domain();
        }
        return array_search($db, CDatabase::$instances[$domain], TRUE);
    }

    /**
     * Sets up the database configuration, loads the CDatabase_Driver.
     *
     * @throws  CDatabase_Exception
     */
    public function __construct($config = array(), $domain = null) {

        if ($domain == null) {
            $domain = CF::domain();
        }
        $load_config = true;

        if (!empty($config)) {
            if (is_array($config) && count($config) > 0) {
                if (!array_key_exists('connection', $config)) {
                    $config = array('connection' => $config);
                    $load_config = false;
                } else {
                    $load_config = false;
                }
            }
            if (is_string($config)) {
                if (strpos($config, '://') !== FALSE) {
                    $config = array('connection' => $config);
                    $load_config = false;
                }
            }
        }

        if ($load_config) {
            $file = CF::get_file('config', 'database', $domain);


            $found = false;
            $config_name = 'default';
            if (is_string($config)) {
                $config_name = $config;
            }

            $all_config = include $file;

            if (isset($all_config[$config_name])) {
                $config = $all_config[$config_name];
                $found = true;
            }


            if ($found == false) {
                throw new Exception('Config ' . $config_name . ' Not Found');
            } else {
                $this->config_file = $file;
            }
        }


        // Merge the default config with the passed config
        $this->config = array_merge($this->config, $config);

        if (is_string($this->config['connection'])) {

            // Make sure the connection is valid
            if (strpos($this->config['connection'], '://') === FALSE)
                throw new CDatabase_Exception('The DSN you supplied is not valid: :dsn', array(':dsn' => $this->config['connection']));

            // Parse the DSN, creating an array to hold the connection parameters
            $db = array
                (
                'type' => FALSE,
                'user' => FALSE,
                'pass' => FALSE,
                'host' => FALSE,
                'port' => FALSE,
                'socket' => FALSE,
                'database' => FALSE
            );

            // Get the protocol and arguments
            list ($db['type'], $connection) = explode('://', $this->config['connection'], 2);

            if (strpos($connection, '@') !== FALSE) {
                // Get the username and password
                list ($db['pass'], $connection) = explode('@', $connection, 2);
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
                    list ($db['socket'], $connection) = explode(')', substr($connection, 5), 2);
                } elseif (strpos($connection, ':') !== FALSE) {
                    // Fetch the host and port name
                    list ($db['host'], $db['port']) = explode(':', $connection, 2);
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
        $driver = 'CDatabase_Driver_' . ucfirst($this->config['connection']['type']);

        try {
            // Validation of the driver
            $class = new ReflectionClass($driver);
            // Initialize the driver
            $this->driver = $class->newInstance($this->config);
        } catch (ReflectionException $ex) {
            throw new CDatabase_Exception('The :driver driver for the :class library could not be found', array(':driver' => $this->config['connection']['type'], 'class' => get_class($this)));
        }

        $connectionResolver = new CDatabase_Resolver(array($this->name => $this));
        CModel::setConnectionResolver($connectionResolver);



        $this->events = new CDatabase_Event();

        $this->configuration = new CDatabase_Configuration();

        // Validate the driver
        if (!($this->driver instanceof CDatabase_Driver)) {
            throw new CDatabase_Exception('The :driver driver for the :class library must implement the :interface interface', array(':driver' => $this->config['connection']['type'], ':class' => get_class($this), ':interface' => 'CDatabase_Driver'));
        }

        CF::log(CLogger::DEBUG, 'Database Library initialized');
    }

    /**
     * Simple connect method to get the database queries up and running.
     *
     * @return  void
     */
    public function connect() {
        // A link can be a resource or an object
        if (!is_resource($this->link) AND ! is_object($this->link)) {
            $this->link = $this->driver->connect();
            if (!is_resource($this->link) AND ! is_object($this->link))
                throw new CDatabase_Exception('There was an error connecting to the database: :error', array(':error' => $this->driver->show_error()));

            // Clear password after successful connect
            $this->config['connection']['pass'] = NULL;
        }
    }

    public function close() {
        $this->driver->close();
        $this->link = null;
    }

    /**
     * Runs a query into the driver and returns the result.
     *
     * @param   string  SQL query to execute
     * @return  CDatabase_Result
     */
    public function query($sql = '', $bindings = array(), Closure $callback = null) {
        if ($sql == '')
            return FALSE;

        // No link? Connect!
        $this->link or $this->connect();

        // Start the benchmark
        $start = microtime(TRUE);


        // Compile binds if needed

        $sql = $this->compileBinds($sql, $bindings);


        // Fetch the result
        $result = $this->driver->query($this->last_query = $sql);

        // Stop the benchmark
        $elapsedTime = $this->getElapsedTime($start);

        $is_benchmark = carr::get($this->config, 'benchmark', FALSE);
        if ($is_benchmark) {
            // Benchmark the query
            CDatabase::$benchmarks[] = array('query' => $sql, 'time' => $elapsedTime, 'rows' => count($result), 'caller' => cdbg::caller_info());
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
     * @param  int    $start
     * @return float
     */
    protected function getElapsedTime($start) {
        return round((microtime(true) - $start) * 1000, 2);
    }

    /**
     * Prepare the query bindings for execution.
     *
     * @param  array  $bindings
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
     * Selects the column names for a database query.
     *
     * @param   string  string or array of column names to select
     * @return  Database_Core  This Database object.
     */
    public function select($sql = '*') {
        if (func_num_args() > 1) {
            $sql = func_get_args();
        } elseif (is_string($sql)) {
            $sql = explode(',', $sql);
        } else {
            $sql = (array) $sql;
        }

        foreach ($sql as $val) {
            if (($val = trim($val)) === '')
                continue;

            if (strpos($val, '(') === FALSE AND $val !== '*') {
                if (preg_match('/^DISTINCT\s++(.+)$/i', $val, $matches)) {
                    // Only prepend with table prefix if table name is specified
                    $val = (strpos($matches[1], '.') !== FALSE) ? $this->config['table_prefix'] . $matches[1] : $matches[1];

                    $this->distinct = TRUE;
                } else {
                    $val = (strpos($val, '.') !== FALSE) ? $this->config['table_prefix'] . $val : $val;
                }

                $val = $this->driver->escape_column($val);
            }

            $this->select[] = $val;
        }

        return $this;
    }

    /**
     * Selects the from table(s) for a database query.
     *
     * @param   string  string or array of tables to select
     * @return  Database_Core  This Database object.
     */
    public function from($sql) {
        if (func_num_args() > 1) {
            $sql = func_get_args();
        } elseif (is_string($sql)) {
            $sql = explode(',', $sql);
        } else {
            $sql = array($sql);
        }

        foreach ($sql as $val) {
            if (is_string($val)) {
                if (($val = trim($val)) === '')
                    continue;

                // TODO: Temporary solution, this should be moved to database driver (AS is checked for twice)
                if (stripos($val, ' AS ') !== FALSE) {
                    $val = str_ireplace(' AS ', ' AS ', $val);

                    list($table, $alias) = explode(' AS ', $val);

                    // Attach prefix to both sides of the AS
                    $val = $this->config['table_prefix'] . $table . ' AS ' . $this->config['table_prefix'] . $alias;
                } else {
                    $val = $this->config['table_prefix'] . $val;
                }
            }

            $this->from[] = $val;
        }

        return $this;
    }

    /**
     * Generates the JOIN portion of the query.
     *
     * @param   string        table name
     * @param   string|array  where key or array of key => value pairs
     * @param   string        where value
     * @param   string        type of join
     * @return  Database_Core        This Database object.
     */
    public function join($table, $key, $value = NULL, $type = '') {
        $join = array();

        if (!empty($type)) {
            $type = strtoupper(trim($type));

            if (!in_array($type, array('LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'), TRUE)) {
                $type = '';
            } else {
                $type .= ' ';
            }
        }

        $cond = array();
        $keys = is_array($key) ? $key : array($key => $value);
        foreach ($keys as $key => $value) {
            $key = (strpos($key, '.') !== FALSE) ? $this->config['table_prefix'] . $key : $key;

            if (is_string($value)) {
                // Only escape if it's a string
                $value = $this->driver->escape_column($this->config['table_prefix'] . $value);
            }

            $cond[] = $this->driver->where($key, $value, 'AND ', count($cond), FALSE);
        }

        if (!is_array($this->join)) {
            $this->join = array();
        }

        if (!is_array($table)) {
            $table = array($table);
        }

        foreach ($table as $t) {
            if (is_string($t)) {
                // TODO: Temporary solution, this should be moved to database driver (AS is checked for twice)
                if (stripos($t, ' AS ') !== FALSE) {
                    $t = str_ireplace(' AS ', ' AS ', $t);

                    list($table, $alias) = explode(' AS ', $t);

                    // Attach prefix to both sides of the AS
                    $t = $this->config['table_prefix'] . $table . ' AS ' . $this->config['table_prefix'] . $alias;
                } else {
                    $t = $this->config['table_prefix'] . $t;
                }
            }

            $join['tables'][] = $this->driver->escape_column($t);
        }

        $join['conditions'] = '(' . trim(implode(' ', $cond)) . ')';
        $join['type'] = $type;

        $this->join[] = $join;

        return $this;
    }

    /**
     * Selects the where(s) for a database query.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @param   boolean       disable quoting of WHERE clause
     * @return  Database_Core        This Database object.
     */
    public function where($key, $value = NULL, $quote = TRUE) {
        $quote = (func_num_args() < 2 AND ! is_array($key)) ? -1 : $quote;
        if (is_object($key)) {
            $keys = array((string) $key => '');
        } elseif (!is_array($key)) {
            $keys = array($key => $value);
        } else {
            $keys = $key;
        }

        foreach ($keys as $key => $value) {
            $key = (strpos($key, '.') !== FALSE) ? $this->config['table_prefix'] . $key : $key;
            $this->where[] = $this->driver->where($key, $value, 'AND ', count($this->where), $quote);
        }

        return $this;
    }

    /**
     * Selects the or where(s) for a database query.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @param   boolean       disable quoting of WHERE clause
     * @return  Database_Core        This Database object.
     */
    public function orwhere($key, $value = NULL, $quote = TRUE) {
        $quote = (func_num_args() < 2 AND ! is_array($key)) ? -1 : $quote;
        if (is_object($key)) {
            $keys = array((string) $key => '');
        } elseif (!is_array($key)) {
            $keys = array($key => $value);
        } else {
            $keys = $key;
        }

        foreach ($keys as $key => $value) {
            $key = (strpos($key, '.') !== FALSE) ? $this->config['table_prefix'] . $key : $key;
            $this->where[] = $this->driver->where($key, $value, 'OR ', count($this->where), $quote);
        }

        return $this;
    }

    /**
     * Selects the like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @param   boolean       automatically add starting and ending wildcards
     * @return  Database_Core        This Database object.
     */
    public function like($field, $match = '', $auto = TRUE) {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->like($field, $match, $auto, 'AND ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the or like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @param   boolean       automatically add starting and ending wildcards
     * @return  Database_Core        This Database object.
     */
    public function orlike($field, $match = '', $auto = TRUE) {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->like($field, $match, $auto, 'OR ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the not like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @param   boolean       automatically add starting and ending wildcards
     * @return  Database_Core        This Database object.
     */
    public function notlike($field, $match = '', $auto = TRUE) {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->notlike($field, $match, $auto, 'AND ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the or not like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @return  Database_Core        This Database object.
     */
    public function ornotlike($field, $match = '', $auto = TRUE) {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->notlike($field, $match, $auto, 'OR ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @return  Database_Core        This Database object.
     */
    public function regex($field, $match = '') {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->regex($field, $match, 'AND ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the or like(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        like value to match with field
     * @return  Database_Core        This Database object.
     */
    public function orregex($field, $match = '') {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->regex($field, $match, 'OR ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the not regex(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        regex value to match with field
     * @return  Database_Core        This Database object.
     */
    public function notregex($field, $match = '') {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->notregex($field, $match, 'AND ', count($this->where));
        }

        return $this;
    }

    /**
     * Selects the or not regex(s) for a database query.
     *
     * @param   string|array  field name or array of field => match pairs
     * @param   string        regex value to match with field
     * @return  Database_Core        This Database object.
     */
    public function ornotregex($field, $match = '') {
        $fields = is_array($field) ? $field : array($field => $match);

        foreach ($fields as $field => $match) {
            $field = (strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] . $field : $field;
            $this->where[] = $this->driver->notregex($field, $match, 'OR ', count($this->where));
        }

        return $this;
    }

    /**
     * Chooses the column to group by in a select query.
     *
     * @param   string  column name to group by
     * @return  Database_Core  This Database object.
     */
    public function groupby($by) {
        if (!is_array($by)) {
            $by = explode(',', (string) $by);
        }

        foreach ($by as $val) {
            $val = trim($val);

            if ($val != '') {
                // Add the table prefix if we are using table.column names
                if (strpos($val, '.')) {
                    $val = $this->config['table_prefix'] . $val;
                }

                $this->groupby[] = $this->driver->escape_column($val);
            }
        }

        return $this;
    }

    /**
     * Selects the having(s) for a database query.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @param   boolean       disable quoting of WHERE clause
     * @return  Database_Core        This Database object.
     */
    public function having($key, $value = '', $quote = TRUE) {
        $this->having[] = $this->driver->where($key, $value, 'AND', count($this->having), TRUE);
        return $this;
    }

    /**
     * Selects the or having(s) for a database query.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @param   boolean       disable quoting of WHERE clause
     * @return  Database_Core        This Database object.
     */
    public function orhaving($key, $value = '', $quote = TRUE) {
        $this->having[] = $this->driver->where($key, $value, 'OR', count($this->having), TRUE);
        return $this;
    }

    /**
     * Chooses which column(s) to order the select query by.
     *
     * @param   string|array  column(s) to order on, can be an array, single column, or comma seperated list of columns
     * @param   string        direction of the order
     * @return  Database_Core        This Database object.
     */
    public function orderby($orderby, $direction = NULL) {
        if (!is_array($orderby)) {
            $orderby = array($orderby => $direction);
        }

        foreach ($orderby as $column => $direction) {
            $direction = strtoupper(trim($direction));

            // Add a direction if the provided one isn't valid
            if (!in_array($direction, array('ASC', 'DESC', 'RAND()', 'RANDOM()', 'NULL'))) {
                $direction = 'ASC';
            }

            // Add the table prefix if a table.column was passed
            if (strpos($column, '.')) {
                $column = $this->config['table_prefix'] . $column;
            }

            $this->orderby[] = $this->driver->escape_column($column) . ' ' . $direction;
        }

        return $this;
    }

    /**
     * Selects the limit section of a query.
     *
     * @param   integer  number of rows to limit result to
     * @param   integer  offset in result to start returning rows from
     * @return  Database_Core   This Database object.
     */
    public function limit($limit, $offset = NULL) {
        $this->limit = (int) $limit;

        if ($offset !== NULL OR ! is_int($this->offset)) {
            $this->offset($offset);
        }

        return $this;
    }

    /**
     * Sets the offset portion of a query.
     *
     * @param   integer  offset value
     * @return  Database_Core   This Database object.
     */
    public function offset($value) {
        $this->offset = (int) $value;

        return $this;
    }

    /**
     * Allows key/value pairs to be set for inserting or updating.
     *
     * @param   string|array  key name or array of key => value pairs
     * @param   string        value to match with key
     * @return  Database_Core        This Database object.
     */
    public function set($key, $value = '') {
        if (!is_array($key)) {
            $key = array($key => $value);
        }

        foreach ($key as $k => $v) {
            // Add a table prefix if the column includes the table.
            if (strpos($k, '.'))
                $k = $this->config['table_prefix'] . $k;

            $this->set[$k] = $this->driver->escape($v);
        }

        return $this;
    }

    /**
     * Compiles the select statement based on the other functions called and runs the query.
     *
     * @param   string  table name
     * @param   string  limit clause
     * @param   string  offset clause
     * @return  CDatabase_Result
     */
    public function get($table = '', $limit = NULL, $offset = NULL) {
        if ($table != '') {
            $this->from($table);
        }

        if (!is_null($limit)) {
            $this->limit($limit, $offset);
        }

        $sql = $this->driver->compile_select(get_object_vars($this));

        $this->reset_select();

        $result = $this->query($sql);

        $this->last_query = $sql;

        return $result;
    }

    /**
     * Compiles the select statement based on the other functions called and runs the query.
     *
     * @param   string  table name
     * @param   array   where clause
     * @param   string  limit clause
     * @param   string  offset clause
     * @return  Database_Core  This Database object.
     */
    public function getwhere($table = '', $where = NULL, $limit = NULL, $offset = NULL) {
        if ($table != '') {
            $this->from($table);
        }

        if (!is_null($where)) {
            $this->where($where);
        }

        if (!is_null($limit)) {
            $this->limit($limit, $offset);
        }

        $sql = $this->driver->compile_select(get_object_vars($this));

        $this->reset_select();

        $result = $this->query($sql);

        return $result;
    }

    /**
     * Compiles the select statement based on the other functions called and returns the query string.
     *
     * @param   string  table name
     * @param   string  limit clause
     * @param   string  offset clause
     * @return  string  sql string
     */
    public function compile($table = '', $limit = NULL, $offset = NULL) {
        if ($table != '') {
            $this->from($table);
        }

        if (!is_null($limit)) {
            $this->limit($limit, $offset);
        }

        $sql = $this->driver->compile_select(get_object_vars($this));

        $this->reset_select();

        return $sql;
    }

    /**
     * Compiles an insert string and runs the query.
     *
     * @param   string  table name
     * @param   array   array of key/value pairs to insert
     * @return  CDatabase_Result  Query result
     */
    public function insert($table = '', $set = NULL) {
        if (!is_null($set)) {
            $this->set($set);
        }

        if ($this->set == NULL)
            throw new CDatabase_Exception('You must set a SET clause for your query');

        if ($table == '') {
            if (!isset($this->from[0]))
                throw new CDatabase_Exception('You must set a database table for your query');

            $table = $this->from[0];
        }

        // If caching is enabled, clear the cache before inserting
        ($this->config['cache'] === TRUE) and $this->clear_cache();

        $sql = $this->driver->insert($this->config['table_prefix'] . $table, array_keys($this->set), array_values($this->set));

        $this->reset_write();

        return $this->query($sql);
    }

    /**
     * Adds an "IN" condition to the where clause
     *
     * @param   string  Name of the column being examined
     * @param   mixed   An array or string to match against
     * @param   bool    Generate a NOT IN clause instead
     * @return  Database_Core  This Database object.
     */
    public function in($field, $values, $not = FALSE) {
        if (is_array($values)) {
            $escaped_values = array();
            foreach ($values as $v) {
                if (is_numeric($v)) {
                    $escaped_values[] = $v;
                } else {
                    $escaped_values[] = "'" . $this->driver->escape_str($v) . "'";
                }
            }
            $values = implode(",", $escaped_values);
        }

        $where = $this->driver->escape_column(((strpos($field, '.') !== FALSE) ? $this->config['table_prefix'] : '') . $field) . ' ' . ($not === TRUE ? 'NOT ' : '') . 'IN (' . $values . ')';
        $this->where[] = $this->driver->where($where, '', 'AND ', count($this->where), -1);

        return $this;
    }

    /**
     * Adds a "NOT IN" condition to the where clause
     *
     * @param   string  Name of the column being examined
     * @param   mixed   An array or string to match against
     * @return  Database_Core  This Database object.
     */
    public function notin($field, $values) {
        return $this->in($field, $values, TRUE);
    }

    /**
     * Compiles a merge string and runs the query.
     *
     * @param   string  table name
     * @param   array   array of key/value pairs to merge
     * @return  CDatabase_Result  Query result
     */
    public function merge($table = '', $set = NULL) {
        if (!is_null($set)) {
            $this->set($set);
        }

        if ($this->set == NULL)
            throw new CDatabase_Exception('You must set a SET clause for your query');

        if ($table == '') {
            if (!isset($this->from[0]))
                throw new CDatabase_Exception('You must set a database table for your query');

            $table = $this->from[0];
        }

        $sql = $this->driver->merge($this->config['table_prefix'] . $table, array_keys($this->set), array_values($this->set));

        $this->reset_write();
        return $this->query($sql);
    }

    /**
     * Compiles an update string and runs the query.
     *
     * @param   string  table name
     * @param   array   associative array of update values
     * @param   array   where clause
     * @return  CDatabase_Result  Query result
     */
    public function update($table = '', $set = NULL, $where = NULL) {
        if (is_array($set)) {
            $this->set($set);
        }

        if (!is_null($where)) {
            $this->where($where);
        }

        if ($this->set == FALSE)
            throw new CDatabase_Exception('You must set a SET clause for your query');

        if ($table == '') {
            if (!isset($this->from[0]))
                throw new CDatabase_Exception('You must set a database table for your query');

            $table = $this->from[0];
        }

        $sql = $this->driver->update($this->config['table_prefix'] . $table, $this->set, $this->where);

        $this->reset_write();
        return $this->query($sql);
    }

    /**
     * Compiles a delete string and runs the query.
     *
     * @param   string  table name
     * @param   array   where clause
     * @return  CDatabase_Result  Query result
     */
    public function delete($table = '', $where = NULL) {
        if ($table == '') {
            if (!isset($this->from[0]))
                throw new CDatabase_Exception('You must set a database table for your query');

            $table = $this->from[0];
        }
        else {
            $table = $this->config['table_prefix'] . $table;
        }

        if (!is_null($where)) {
            $this->where($where);
        }

        if (count($this->where) < 1)
            throw new CDatabase_Exception('You must set a WHERE clause for your query');

        $sql = $this->driver->delete($table, $this->where);

        $this->reset_write();
        return $this->query($sql);
    }

    /**
     * Returns the last query run.
     *
     * @return  string SQL
     */
    public function last_query() {
        return $this->last_query;
    }

    /**
     * Count query records.
     *
     * @param   string   table name
     * @param   array    where clause
     * @return  integer
     */
    public function count_records($table = FALSE, $where = NULL) {
        if (count($this->from) < 1) {
            if ($table == FALSE)
                throw new CDatabase_Exception('You must set a database table for your query');

            $this->from($table);
        }

        if ($where !== NULL) {
            $this->where($where);
        }

        $query = $this->select('COUNT(*) AS ' . $this->escape_column('records_found'))->get()->result(TRUE);

        return (int) $query->current()->records_found;
    }

    /**
     * Resets all private select variables.
     *
     * @return  void
     */
    protected function reset_select() {
        $this->select = array();
        $this->from = array();
        $this->join = array();
        $this->where = array();
        $this->orderby = array();
        $this->groupby = array();
        $this->having = array();
        $this->distinct = FALSE;
        $this->limit = FALSE;
        $this->offset = FALSE;
    }

    /**
     * Resets all private insert and update variables.
     *
     * @return  void
     */
    protected function reset_write() {
        $this->set = array();
        $this->from = array();
        $this->where = array();
    }

    /**
     * Lists all the tables in the current database.
     *
     * @return  array
     */
    public function list_tables() {
        $this->link or $this->connect();

        return $this->driver->list_tables();
    }

    /**
     * See if a table exists in the database.
     *
     * @param   string   table name
     * @param   boolean  True to attach table prefix
     * @return  boolean
     */
    public function table_exists($table_name, $prefix = TRUE) {
        if ($prefix)
            return in_array($this->config['table_prefix'] . $table_name, $this->list_tables());
        else
            return in_array($table_name, $this->list_tables());
    }

    /**
     * Combine a SQL statement with the bind values. Used for safe queries.
     *
     * @param   string  query to bind to the values
     * @param   array   array of values to bind to the query
     * @return  string
     */
    public function compileBinds($sql, $binds) {
        foreach ((array) $binds as $val) {
            // If the SQL contains no more bind marks ("?"), we're done.
            if (($next_bind_pos = strpos($sql, '?')) === FALSE)
                break;

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
     * @param   string  table name
     * @return  array
     */
    public function field_data($table = '') {
        $this->link or $this->connect();

        return $this->driver->field_data($this->config['table_prefix'] . $table);
    }

    /**
     * Get the field data for a database table, along with the field's attributes.
     *
     * @param   string  table name
     * @return  array
     */
    public function list_fields($table = '') {
        $this->link or $this->connect();

        return $this->driver->list_fields($this->config['table_prefix'] . $table);
    }

    /**
     * Escapes a value for a query.
     *
     * @param   mixed   value to escape
     * @return  string
     */
    public function escape($value) {
        return $this->driver->escape($value);
    }

    /**
     * Escapes a string for a query.
     *
     * @param   string  string to escape
     * @return  string
     */
    public function escape_str($str) {
        return $this->driver->escape_str($str);
    }

    /**
     * Escapes a table name for a query.
     *
     * @param   string  string to escape
     * @return  string
     */
    public function escape_table($table) {
        return $this->driver->escape_table($table);
    }

    /**
     * Escapes a column name for a query.
     *
     * @param   string  string to escape
     * @return  string
     */
    public function escape_column($table) {
        return $this->driver->escape_column($table);
    }

    /**
     * Returns table prefix of current configuration.
     *
     * @return  string
     */
    public function table_prefix() {
        return $this->config['table_prefix'];
    }

    /**
     * Clears the query cache.
     *
     * @param   string|TRUE  clear cache by SQL statement or TRUE for last query
     * @return  Database_Core       This Database object.
     */
    public function clear_cache($sql = NULL) {
        if ($sql === TRUE) {
            $this->driver->clear_cache($this->last_query);
        } elseif (is_string($sql)) {
            $this->driver->clear_cache($sql);
        } else {
            $this->driver->clear_cache();
        }

        return $this;
    }

    /**
     * Pushes existing query space onto the query stack.  Use push
     * and pop to prevent queries from clashing before they are
     * executed
     *
     * @return Database_Core This Databaes object
     */
    public function push() {
        array_push($this->query_history, array(
            $this->select,
            $this->from,
            $this->join,
            $this->where,
            $this->orderby,
            $this->order,
            $this->groupby,
            $this->having,
            $this->distinct,
            $this->limit,
            $this->offset
        ));

        $this->reset_select();

        return $this;
    }

    /**
     * Pops from query stack into the current query space.
     *
     * @return Database_Core This Databaes object
     */
    public function pop() {
        if (count($this->query_history) == 0) {
            // No history
            return $this;
        }

        list(
                $this->select,
                $this->from,
                $this->join,
                $this->where,
                $this->orderby,
                $this->order,
                $this->groupby,
                $this->having,
                $this->distinct,
                $this->limit,
                $this->offset
                ) = array_pop($this->query_history);

        return $this;
    }

    /**
     * Count the number of records in the last query, without LIMIT or OFFSET applied.
     *
     * @return  integer
     */
    public function count_last_query() {
        if ($sql = $this->last_query()) {
            if (stripos($sql, 'LIMIT') !== FALSE) {
                // Remove LIMIT from the SQL
                $sql = preg_replace('/\sLIMIT\s+[^a-z]+/i', ' ', $sql);
            }

            if (stripos($sql, 'OFFSET') !== FALSE) {
                // Remove OFFSET from the SQL
                $sql = preg_replace('/\sOFFSET\s+\d+/i', '', $sql);
            }

            // Get the total rows from the last query executed
            $result = $this->query
                    (
                    'SELECT COUNT(*) AS ' . $this->escape_column('total_rows') . ' ' .
                    'FROM (' . trim($sql) . ') AS ' . $this->escape_table('counted_results')
            );

            // Return the total number of rows from the query
            return (int) $result->current()->total_rows;
        }

        return FALSE;
    }

    protected $in_trans = false;

    public function __destruct() {
        self::rollback();
    }

    public function in_transaction() {
        return $this->in_trans;
    }

    public function begin() {
        if (!$this->in_trans)
            $this->query('START TRANSACTION;');
        $this->in_trans = true;
    }

    public function commit() {
        if ($this->in_trans)
            $this->query('COMMIT;');
        $this->in_trans = false;
    }

    public function rollback() {
        if ($this->in_trans)
            $this->query('ROLLBACK;');
        $this->in_trans = false;
    }

    public function escape_like($str) {
        //$str = str_replace(array($e, '_', '%'), array($e.$e, $e.'_', $e.'%'), $s);
        $str = $this->escape_str($str);
        return $str;
    }

    public function driver_name() {
        return ucfirst($this->config['connection']['type']);
    }

    /**
     * Prepares and executes an SQL query and returns the result as an associative array.
     *
     * @param string $sql    The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The query parameter types.
     *
     * @return array
     */
    public function fetchAll($sql, array $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function table($table) {
        return (new CDatabase_Query_Builder($this))->from($table);
    }

    /**
     * Reconnect to the database.
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function reconnect() {
        if (is_callable($this->reconnector)) {
            return call_user_func($this->reconnector, $this);
        }

        throw new LogicException('Lost connection and no reconnector available.');
    }

    /**
     * Register a database query listener with the connection.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public function listen(Closure $callback) {
        if (isset($this->events)) {
            $this->events->listen(CDatabase_Event_OnQueryExecuted::class, $callback);
        }
    }

    public function haveListener() {
        if (isset($this->events)) {
            $this->events->haveListener();
        }
    }

    /**
     * Fire the given event if possible.
     *
     * @param  mixed  $event
     * @return void
     */
    protected function event($event) {
        if (isset($this->events)) {
            $this->events->dispatch($event);
        }
    }

    /**
     * Get the event dispatcher used by the connection.
     *
     * @return CContracts_Events_Dispatcher
     */
    public function getEventDispatcher() {
        return $this->events;
    }

    /**
     * Set the event dispatcher instance on the connection.
     *
     * @param  CContracts_Events_Dispatcher  $events
     * @return void
     */
    public function setEventDispatcher(CContracts_Events_Dispatcher $events) {
        $this->events = $events;
    }

    /**
     * Get the query grammar used by the connection.
     *
     * @return CDatabase_Query_Grammar
     */
    public function getQueryGrammar() {

        if ($this->queryGrammar == null) {
            $driver_name = $this->driver_name();
            $grammar_class = 'CDatabase_Query_Grammar_' . $driver_name;
            $this->queryGrammar = new $grammar_class();
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
     * @return CDatabase_Schema_AbstractSchemaManager
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
     * @return CDatabase_Platform
     *
     * @throws CDatabase_Exception
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
     * @throws CDatabase_Exception if an invalid platform was specified for this connection.
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
     * @return string|null
     *
     * @throws Exception
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
     * @return string|null
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

    /**
     * Log a query in the connection's query log.
     *
     * @param  string  $query
     * @param  array   $bindings
     * @param  float|null  $time
     * @return void
     */
    public function logQuery($query, $bindings, $time = null, $rowsCount = null) {
        $this->event(new CDatabase_Event_OnQueryExecuted($query, $bindings, $time, $rowsCount, $this));

        if ($this->isLogQuery()) {
            $this->queryLog[] = compact('query', 'bindings', 'time');
        }
    }

    /**
     * Get the name of the connected database.
     *
     * @return string
     */
    public function getDatabaseName() {
        return carr::path($this->config, 'connection.database');
    }

    public function getRow($query) {
        $r = $this->query($query);
        $result = null;
        if ($r->count() > 0) {
            $result = $r[0];
        }
        return $result;
    }

    public static function getValue($query) {
        $r = $this->query($query);
        $result = $r->result(false);
        $res = array();
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

    public static function getArray($query) {
        $r = $this->query($query);
        $result = $r->result(false);
        $res = array();
        foreach ($result as $row) {
            $cnt = 0;
            $arr_val = "";
            foreach ($row as $k => $v) {
                if ($cnt == 0)
                    $arr_val = $v;
                $cnt++;
                if ($cnt > 0)
                    break;
            }
            $res[] = $arr_val;
        }
        return $res;
    }

    public static function getList($query) {
        $r = $this->query($query);
        $result = $r->result(false);
        $res = array();
        foreach ($result as $row) {
            $cnt = 0;
            $arr_key = "";
            $arr_val = "";
            foreach ($row as $k => $v) {
                if ($cnt == 0)
                    $arr_key = $v;
                if ($cnt == 1)
                    $arr_val = $v;
                $cnt++;
                if ($cnt > 1)
                    break;
            }
            $res[$arr_key] = $arr_val;
        }
        return $res;
    }

}

// End Database Class



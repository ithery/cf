<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use MongoDB\Client;

class CDatabase_Driver_MongoDB extends CDatabase_Driver {

    /**
     * Database connection link
     */
    protected $link;

    /**
     * @var CDatabase 
     */
    protected $db;

    /**
     *
     * @var \MongoDB\Database
     */
    protected $mongoDB;
    /**
     * Database configuration
     */
    protected $dbConfig;

    /**
     * Sets the config for the class.
     *
     * @param  array  database configuration
     */
    public function __construct(CDatabase $db, $config) {
        $this->db = $db;
        $this->dbConfig = $config;
        $this->connect();
        CF::log(CLogger::DEBUG, 'MongoDB Database Driver Initialized');
    }

    /**
     * Closes the database connection.
     */
    public function __destruct() {
        unset($this->link);
        $this->link = null;
    }

    /**
     * Determine if the given configuration array has a dsn string.
     * @param array $config
     * @return bool
     */
    protected function hasDsnString(array $config = null) {
        if ($config == null) {
            $config = $this->dbConfig;
        }
        return isset($config['dsn']) && !empty($config['dsn']);
    }

    /**
     * Get the DSN string form configuration.
     * @param array $config
     * @return string
     */
    protected function getDsnString(array $config = null) {
        if ($config == null) {
            $config = $this->dbConfig;
        }
        return carr::get($config, 'dsn');
    }

    /**
     * Get the DSN string for a host / port configuration.
     * @param array $config
     * @return string
     */
    protected function getHostDsn(array $config = null) {
        if ($config == null) {
            $config = $this->dbConfig;
        }

        $configConnection = carr::get($config, 'connection');

        // Treat host option as array of hosts
        $hosts = is_array($configConnection['host']) ? $configConnection['host'] : [$configConnection['host']];
        foreach ($hosts as &$host) {
            // Check if we need to add a port to the host
            if (strpos($host, ':') === false && !empty($configConnection['port'])) {
                $host = $host . ':' . $configConnection['port'];
            }
        }
        // Check if we want to authenticate against a specific database.
        $auth_database = isset($configConnection['options']) && !empty($configConnection['options']['database']) ? $configConnection['options']['database'] : null;
        return 'mongodb://' . implode(',', $hosts) . ($auth_database ? '/' . $auth_database : '');
    }

    /**
     * Create a DSN string from a configuration.
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config = null) {
        if ($config == null) {
            $config = $this->dbConfig;
        }
        return $this->hasDsnString($config) ? $this->getDsnString($config) : $this->getHostDsn($config);
    }

    /**
     * Dynamically pass methods to the connection.
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return call_user_func_array([$this->link, $method], $parameters);
    }

    public function connect() {
        // Check if link already exists
        if (($this->link != null)) {
            return $this->link;
        }

        $dsn = $this->getDsn();
       
        $options = carr::get($this->dbConfig, 'options',[]);
        
        $this->link = $this->createConnection($dsn, $this->dbConfig, $options);
        $this->mongoDB = $this->link->selectDatabase(carr::get($this->dbConfig,'connection.database'));
        return $this->link;
    }

    /**
     * Create a new MongoDB connection.
     * @param string $dsn
     * @param array $config
     * @param array $options
     * @return \MongoDB\Client
     */
    protected function createConnection($dsn, array $config, array $options) {
        // By default driver options is an empty array.

        $connectionData = carr::get($config, 'connection');
        $driverOptions = [];
        if (isset($config['driver_options']) && is_array($config['driver_options'])) {
            $driverOptions = $config['driver_options'];
        }
        // Check if the credentials are not already set in the options
        if (!isset($options['username']) && !empty($connectionData['username'])) {
            $options['username'] = $connectionData['username'];
        }
        if (!isset($options['password']) && !empty($connectionData['password'])) {
            $options['password'] = $connectionData['password'];
        }
        return new Client($dsn, $options, $driverOptions);
    }

    public function compile_select($database) {
        
    }

    public function escape_column($column) {
        
    }

    public function escape_str($str) {
        return $str;
    }

    public function escape_table($table) {
        return $table;
    }

    public function field_data($table) {
        
    }

    public function limit($limit, $offset = 0) {
        
    }

    public function list_fields($table) {
        
    }

    public function list_tables() {
        
    }

    public function query($sql) {
        
    }

    public function show_error() {
        
    }

    /**
     * Get a MongoDB collection.
     * @param string $name
     * @return Collection
     */
    public function getCollection($name) {
        return new CDatabase_Driver_MongoDB_Collection($this , $this->mongoDB->selectCollection($name));
    }

}

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
     * Database configuration
     */
    protected $dbCconfig;

    /**
     * Sets the config for the class.
     *
     * @param  array  database configuration
     */
    public function __construct($config) {
        $this->dbConfig = $config;

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
            $config = $this->dbCconfig;
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
            $config = $this->dbCconfig;
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
            $config = $this->dbCconfig;
        }
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
     * @param array $config
     * @return string
     */
    protected function getDsn(array $config = null) {
        if ($config == null) {
            $config = $this->dbCconfig;
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
        $options = carr::get($this->dbCconfig, 'options');

        $this->link = $this->createConnection($dsn, $this->dbConfig, $options);

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
        
        $connectionData=carr::get($config,'connection');
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
        
    }

    public function escape_table($table) {
        
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

}

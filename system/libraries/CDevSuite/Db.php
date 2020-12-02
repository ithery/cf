<?php

/**
 * Description of Db
 *
 * @author Hery
 */
class CDevSuite_Db {

    public $configuration;

    /**
     * Create a new Nginx instance.
     * @return void
     */
    function __construct() {

        $this->files = CDevSuite::filesystem();
    }

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    public function path() {
        return CDevSuite::homePath() . '/db.json';
    }

    public function ensureFileExists() {
        if (!$this->files->exists($this->path())) {
            $this->write([]);
        }
    }

    public function create($name, $configuration) {


        if (!$this->isCanConnect($configuration)) {
            CDevSuite::info('Error when connecting to:' . $name . ', please check your configuration');
            return false;
        }
        $data = $this->read();
        $data[$name] = $configuration;

        $this->write($data);
        return true;
    }

    /**
     * Write the given configuration to disk.
     *
     * @param array $config
     *
     * @return void
     */
    public function write($data) {
        $this->files->putAsUser($this->path(), json_encode(
                        $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                ) . PHP_EOL);
    }

    /**
     * Read the configuration file as JSON.
     *
     * @return array
     */
    public function read() {
        $this->ensureFileExists();
        return json_decode($this->files->get($this->path()), true);
    }

    public function getTableData() {
        $data = $this->read();


        return c::collect($data)->map(function ($item) {
                    return [
                        'type' => carr::get($item, 'type'),
                        'database' => carr::get($item, 'database'),
                        'host' => carr::get($item, 'host') . ':' . carr::get($item, 'port'),
                        'auth' => carr::get($item, 'user') . ':' . carr::get($item, 'password'),
                    ];
                });
    }

    public function toDbConfig($keyFile) {
        $configArray = $keyFile;
        if (!is_array($configArray)) {
            $configArray = $this->get($keyFile);
        }

        $host = carr::get($configArray, 'host');
        $username = carr::get($configArray, 'user');
        $password = carr::get($configArray, 'password');
        $port = carr::get($configArray, 'port');
        $driver = carr::get($configArray, 'type');
        $database = carr::get($configArray, 'database');


        if (strlen($driver) == 0) {
            $driver = 'mysqli';
        }

        if ($driver == 'mysql') {
            $driver = 'mysqli';
        }

        if ($driver == 'mongodb') {
            if ($database == null) {
                $database = 'admin';
            }
        }
        $config = array(
            'benchmark' => TRUE,
            'persistent' => FALSE,
            'connection' => array(
                'type' => $driver,
                'user' => $username,
                'pass' => $password,
                'host' => $host,
                'port' => $port,
                'socket' => FALSE,
                'database' => $database,
            ),
            'character_set' => 'utf8mb4',
            'table_prefix' => '',
            'object' => TRUE,
            'cache' => FALSE,
            'escape' => TRUE
        );
        return $config;
    }

    /**
     * 
     */
    public function getDatabase($key) {
        $config = $this->toDbConfig($key);
        $host = carr::get($config, 'connection.host');
        $database = carr::get($config, 'connection.database');
        return CDatabase::instance($host . '-' . $database, $config, CF::domain());
    }

    public function isCanConnect($key) {


        try {
            $db = $this->getDatabase($key);
            $db->connect();
        } catch (Exception $ex) {
            $errMessage = $ex->getMessage();
            CDevSuite::info($ex->getMessage());
            return false;
        }
        return true;
    }

}

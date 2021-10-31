<?php

/**
 * Description of Db
 *
 * @author Hery
 */
class CDevSuite_Db {
    public $files;

    protected $mariaDb;

    /**
     * Create a new Nginx instance.
     *
     * @return void
     */
    public function __construct() {
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
     * @param array $data
     *
     * @return void
     */
    public function write($data) {
        $this->files->putAsUser($this->path(), json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
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

        return c::collect($data)->map(function ($item, $key) {
            return [
                'key' => $key,
                'type' => carr::get($item, 'type'),
                'database' => carr::get($item, 'database'),
                'host' => carr::get($item, 'host') . ':' . carr::get($item, 'port'),
                //'auth' => carr::get($item, 'user') . ':' . carr::get($item, 'password'),
            ];
        });
    }

    public function toDbConfig($keyFile) {
        $configArray = $keyFile;
        if (!is_array($configArray)) {
            $configArray = carr::get($this->read(), $keyFile);
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
        $config = [
            'benchmark' => true,
            'persistent' => false,
            'connection' => [
                'type' => $driver,
                'user' => $username,
                'pass' => $password,
                'host' => $host,
                'port' => $port,
                'socket' => false,
                'database' => $database,
            ],
            'character_set' => 'utf8mb4',
            'table_prefix' => '',
            'object' => true,
            'cache' => false,
            'escape' => true
        ];
        return $config;
    }

    /**
     * @param mixed $key
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

    public function compare($from, $to) {
        $fromDB = $this->getDatabase($from);
        $toDB = $this->getDatabase($to);

        $fromDB->connect();
        $toDB->connect();

        $fromSchemaManager = $fromDB->getSchemaManager();
        $toSchemaManager = $toDB->getSchemaManager();

        $fromSchema = $fromSchemaManager->createSchema();
        $toSchema = $toSchemaManager->createSchema();

        $sqls = $toSchema->getMigrateToSql($fromSchema, $fromDB->getDatabasePlatform());

        $sqlString = '';
        $formatted = false;
        foreach ($sqls as $sql) {
            if ($formatted) {
                $sql = CSql::format($sql);
            }
            $sqlString .= PHP_EOL . ($sql . ';') . PHP_EOL;
        }
        if (strlen(trim($sqlString)) == 0) {
            $sqlString = '/* No diff */';
        }

        CDevSuite::info($sqlString);
    }

    public function sync($from, $to) {
        $fromDB = $this->getDatabase($from);
        $toDB = $this->getDatabase($to);

        $fromDB->connect();
        $toDB->connect();

        $fromSchemaManager = $fromDB->getSchemaManager();
        $toSchemaManager = $toDB->getSchemaManager();

        $fromSchema = $fromSchemaManager->createSchema();
        $toSchema = $toSchemaManager->createSchema();

        $sqls = $toSchema->getMigrateToSql($fromSchema, $fromDB->getDatabasePlatform());

        $sqlString = '';
        $resultString = '';
        $haveError = 0;
        $toDB->begin();

        $allError = [];
        foreach ($sqls as $sql) {
            $errSql = 0;
            $resultQ = null;
            try {
                CDevSuite::info('Executing:' . $sql . ';');
                $resultQ = $toDB->query($sql);
            } catch (Exception $ex) {
                CDevSuite::error('Error:' . $ex->getMessage());
                $allError[$sql] = $ex->getMessage();
                $errSql++;
                $haveError++;
            }
            if ($errSql == 0) {
                CDevSuite::info('Success Execute Query');
            }
        }
        if ($haveError) {
            $toDB->rollback();
        } else {
            $toDB->commit();
        }

        if ($haveError) {
            CDevSuite::error('Failed Synchronize database from:' . $from . ' to:' . $to);
            CDevSuite::error('Please check error below:');
            foreach ($allError as $sql => $error) {
                CDevSuite::error('Error:' . $error . ' when executing query:' . $sql);
            }
        } else {
            CDevSuite::info('Success Execute Synchronize database from:' . $from . ' to:' . $to);
        }
    }

    public function exists($key) {
        return is_array(carr::get($this->read(), $key));
    }

    public function existsOrExit($key) {
        if (!$this->exists($key)) {
            CDevSuite::error('Database configuration: ' . $key . ' not exists');
            exit(CConsole::FAILURE_EXIT);
        }
    }

    /**
     * Return Maria DB instance
     *
     * @return CDevSuite_Db_MariaDb
     */
    public function mariaDb() {
        if ($this->mariaDb == null) {
            switch (CServer::getOS()) {
                case CServer::OS_LINUX:
                    $this->mariaDb = new CDevSuite_Linux_Db_MariaDb();
                    break;
                case CServer::OS_WINNT:
                    $this->mariaDb = new CDevSuite_Windows_Db_MariaDb();
                    break;
                case CServer::OS_DARWIN:
                    $this->mariaDb = new CDevSuite_Mac_Db_MariaDb();
                    break;
            }
        }
        return $this->mariaDb;
    }

    public function start() {
        $this->mariaDb()->install();
    }

    public function delete($key) {
        $data = $this->read();
        if (isset($data[$key])) {
            unset($data[$key]);
            $this->write($data);
            return true;
        }
        return false;
    }

    public function cloneDatabase($from, $to) {
        $fromConfig = $this->toDbConfig($from);
        $toConfig = $this->toDbConfig($to);

        CDevSuite::info('Prepare to dumping database:' . $from);
        $dumpFile = $this->mariaDb()->dump($fromConfig);

        $output = $this->mariaDb()->restore($toConfig, $dumpFile);

        CDevSuite::info($output);

        //$this->files->unlink($dumpFile);
    }
}

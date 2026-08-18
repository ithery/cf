<?php

class CQC_Testing_Database {
    protected static $instance;

    protected $path;

    /**
     * @var CQC_Testing_Database_Data
     */
    protected $data;

    protected $connection;

    public static function instance() {
        if (!static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function __construct($path = null) {
        $defaultDir = DOCROOT . 'temp' . DS . 'qc' . DS . CF::appCode() . DS . 'db' . DS;
        if (!CFile::isDirectory($defaultDir)) {
            CFile::makeDirectory($defaultDir, 0755, true);
        }
        if ($path == null) {
            $path = $defaultDir . 'testing.sqlite';
        }

        $dataPath = $defaultDir . 'data-testing.json';
        $this->data = new CQC_Testing_Database_Data($dataPath);
        $this->path = $path;
    }

    public function migrate() {
        if ($this->shouldMigrate()) {
            $migration = new CQC_Testing_Database_Migration($this->path);
            $migration->migrate();
            $this->data->set('datamtime', time());
        }
    }

    private function getModifiedTime() {
        $time1 = filemtime(__FILE__);
        $class = new ReflectionClass(CQC_Testing_Database_Migration::class);
        $time2 = filemtime($class->getFileName());

        return max($time1, $time2);
    }

    public function shouldMigrate() {
        $currentTime = $this->getModifiedTime();
        $datamtime = $this->data->get('datamtime');
        if (!$datamtime) {
            return true;
        }

        return intval($datamtime) < $currentTime;
    }

    public function resolveConnection() {
        if ($this->connection == null) {
            $config = [
                'pdo' => true,
                'type' => 'sqlite',
                'database' => $this->path,
            ];
            CDatabase::manager()->addConnection($config, static::class);
            $this->connection = c::db(static::class);
        }

        return $this->connection;
    }
}

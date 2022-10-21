<?php

class CQC_Testing {
    const STATE_IDLE = 'idle';

    const STATE_QUEUED = 'queued';

    const STATE_OK = 'ok';

    const STATE_FAILED = 'failed';

    const STATE_RUNNING = 'running';

    /**
     * @var CQC_Testing_TestSuite[]
     */
    protected $suites;

    /**
     * @var CQC_Testing_Repository
     */
    protected $repository;

    public function __construct() {
        $this->suites = [];
        $this->repository = new CQC_Testing_Repository();
        if ($this->db()->shouldMigrate()) {
            if ($this->daemonIsRunning()) {
                $this->stopDaemon();
            }

            $this->db()->migrate();
        }
        $this->load();
    }

    public function addSuite($path) {
        $this->suites[] = new CQC_Testing_TestSuite($path);
    }

    /**
     * Get array of CQC_PHPUnit_TestSuite.
     *
     * @return CQC_Testing_TestSuite[]
     */
    public function getTestSuites() {
        return $this->suites;
    }

    public function daemonIsRunning() {
        return CQC_Testing_DaemonManager::instance()->isRunning();
    }

    public function startDaemon() {
        return CQC_Testing_DaemonManager::instance()->start();
    }

    public function stopDaemon() {
        return CQC_Testing_DaemonManager::instance()->stop();
    }

    public function daemonLog() {
        return CQC_Testing_DaemonManager::instance()->getLog();
    }

    /**
     * @return CQC_Testing_Config
     */
    public function config() {
        return CQC_Testing_Config::instance();
    }

    /**
     * @return CQC_Testing_Database
     */
    public function db() {
        return CQC_Testing_Database::instance();
    }

    /**
     * @return CQC_Testing_Repository
     */
    public function repository() {
        return $this->repository;
    }

    private function load() {
        if (CFile::isDirectory($unitPath = c::appRoot('default/tests/Unit'))) {
            $this->addSuite($unitPath);
        }
        $loader = new CQC_Testing_Loader($this->repository);
        $loader->refreshSuites($this->suites);
    }
}

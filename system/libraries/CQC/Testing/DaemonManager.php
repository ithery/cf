<?php

class CQC_Testing_DaemonManager {
    protected static $instance;

    protected $daemonQueueRunner;

    /**
     * @return CQC_Testing_DaemonManager
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->daemonQueueRunner = CDaemon::createRunner(CQC_Testing_Daemon_QueueRunner::class);
    }

    public function isRunning() {
        return $this->daemonQueueRunner->isRunning();
    }

    public function start() {
        $this->daemonQueueRunner->rotateLog();

        return $this->daemonQueueRunner->start();
    }

    public function stop() {
        return $this->daemonQueueRunner->stop();
    }

    public function getLog() {
        return $this->daemonQueueRunner->getLog();
    }
}

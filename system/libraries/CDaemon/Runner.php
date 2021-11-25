<?php

class CDaemon_Runner {
    protected $serviceClass;

    public function __construct($serviceClass) {
        $this->serviceClass = $serviceClass;
    }

    public function getLogFile($className, $filename = null) {
        if ($filename == null) {
            $filename = $className . '.log';
        }

        return CDaemon::logPath() . $className . '/' . $filename;
    }

    public function getPidFile($className) {
        return CDaemon::pidPath() . $className . '.pid';
    }

    public function start() {
    }
}

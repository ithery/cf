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
        $className = $this->serviceClass;
        $config = [];
        $config['serviceClass'] = $className;
        //get last suffix class
        $serviceName = $this->getServiceName($className);
        $config['serviceName'] = $className;
        $config['pidFile'] = $this->getPidFile($className);
        $config['logFile'] = $this->getLogFile($className);
        $config['stdout'] = false;
        $config['command'] = $command;
        $daemon = new CDaemon($config);
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CManager_Daemon {

    protected static $instance;
    protected $daemons = array();

    /**
     * 
     * @return CManager_Daemon
     */
    public static function instance() {

        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function registerDaemon($class, $name = null) {

        if ($name == null) {
            $name = carr::last(explode('_', $class));
        }
        $this->daemons[$class] = $name;
    }

    public function daemons() {
        return $this->daemons;
    }

    protected function getDaemon($className, $command = 'status') {
        $config = array();
        $config['serviceClass'] = $className;
        //get last suffix class
        $serviceName = $this->getServiceName($className);
        $config['serviceName'] = $className;
        $config['pidFile'] = $this->getPidFile($className);
        $config['logFile'] = $this->getLogFile($className);
        $config['stdout'] = false;
        $config['command'] = $command;
        $daemon = new CDaemon($config);
        return $daemon;
    }
    
    public function pidPath() {
        return DOCROOT . 'data/daemon/' . CF::appCode() . '/daemon/pid';
    }

    public function logPath() {
        return DOCROOT . 'data/daemon/' . CF::appCode() . '/log/';
    }
    
    protected function runDaemon($className, $command) {
        $daemon = $this->getDaemon($className, $command);
        return $daemon->run();
    }

    public function status($className) {
        return $this->runDaemon($className, 'status');
    }

    public function start($className) {
        return $this->runDaemon($className, 'start');
    }

    public function stop($className) {
        return $this->runDaemon($className, 'stop');
    }

    public function isRunning($className) {
        $daemon = self::getDaemon($className);
        return $daemon->isRunning();
    }

    public function getServiceName($className) {
        $serviceName = $className;
        $serviceNameExploded = explode('_', $className);
        if ($serviceNameExploded > 0) {
            $serviceName = carr::get($serviceNameExploded, count($serviceNameExploded) - 1);
        }
        return $serviceName;
    }

    public function getLogFile($className) {
        return self::logPath() . $className . '.log';
    }

    public function getPidFile($className) {
        return self::pidPath() . $className . '.pid';
    }

}

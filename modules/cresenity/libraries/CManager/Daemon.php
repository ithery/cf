<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CManager_Daemon {

    protected static $instance;
    protected $daemons = array();
    protected $daemonsGroup = array();

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

    public function registerDaemon($class, $name = null, $group = null) {

        if ($name == null) {
            $name = carr::last(explode('_', $class));
        }
        $this->daemons[$class] = $name;
        if ($group !== null) {
            if (!isset($this->daemonsGroup[$group])) {
                $this->daemonsGroup[$group] = [];
            }
            $this->daemonsGroup[$group][$class] = $name;
        }
    }

    public function daemons($group = null) {
        if ($group === null) {
            return $this->daemons;
        }
        if ($group === false) {
            $allDaemons = $this->daemons;
            foreach ($this->daemonsGroup as $groupArray) {
                $allDaemons = array_diff_key($allDaemons, $groupArray);
            }
            return $allDaemons;
        }
        if ($group !== null) {
            if (!in_array($group, $this->getGroupsKey())) {
                throw new Exception('group daemon ' . $group . ' not available');
            }
        }
        return $this->daemonsGroup[$group];
    }

    public function getGroupsKey() {
        return array_keys($this->daemonsGroup);
    }

    public function haveGroup() {
        return count($this->getGroupsKey()) > 0;
    }

    /**
     * 
     * @param string $className
     * @param string $command
     * @return \CDaemon
     */
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
        return DOCROOT . 'data/daemon/' . CF::appCode() . '/daemon/pid/';
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

    public function rotateLog($className) {
        $daemon = self::getDaemon($className);
        return $daemon->rotateLog();
    }

    public function logDump($className) {
        $daemon = self::getDaemon($className);
        return $daemon->logDump();
    }

    public function getServiceName($className) {
        $serviceName = $className;
        $serviceNameExploded = explode('_', $className);
        if ($serviceNameExploded > 0) {
            $serviceName = carr::get($serviceNameExploded, count($serviceNameExploded) - 1);
        }
        return $serviceName;
    }

    public function getLogFile($className, $filename = null) {
        if ($filename == null) {
            $filename = $className . '.log';
        }
        return self::logPath() . $className . '/' . $filename;
    }

    public function getPidFile($className) {
        return self::pidPath() . $className . '.pid';
    }

    public function getLogFileList($className) {
        $fileHelper = CHelper::file();
        $logPath = rtrim(self::logPath(), '/') . '/' . $className;
        if (!is_dir($logPath)) {
            return [];
        }
        $files = $fileHelper->files($logPath);
        $list = array();
        foreach ($files as $file) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            $basename = $file->getBasename();

            $list[$file->getPath() . DS . $file->getFilename()] = $basename;
        }

        return $list;
    }

}

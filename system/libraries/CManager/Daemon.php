<?php

final class CManager_Daemon {
    protected static $instance;

    protected $daemons = [];

    protected $daemonsGroup = [];

    /**
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

    public function status($className) {
        return CDaemon::createRunner($className)->status();
    }

    public function start($className) {
        return CDaemon::createRunner($className)->run();
    }

    public function stop($className) {
        return CDaemon::createRunner($className)->stop();
    }

    public function isRunning($className) {
        return CDaemon::createRunner($className)->isRunning();
    }

    public function rotateLog($className) {
        return CDaemon::createRunner($className)->rotateLog();
    }

    public function logDump($className) {
        return CDaemon::createRunner($className)->logDump();
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
        return CDaemon_Helper::getLogFile($className, $filename);
    }

    public function getPidFile($className) {
        return CDaemon_Helper::getPidFile($className);
    }

    public function getLogFileList($className) {
        return CDaemon_Helper::getLogFileList($className);
    }
}

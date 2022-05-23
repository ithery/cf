<?php

class CDaemon_Manager {
    public static function getServiceName($className) {
        $serviceName = $className;
        $serviceNameExploded = explode('_', $className);
        if ($serviceNameExploded > 0) {
            $serviceName = carr::get($serviceNameExploded, count($serviceNameExploded) - 1);
        }

        return $serviceName;
    }

    /**
     * Create Service Object From Given Service Class Name.
     *
     * @param string $className
     *
     * @return CDaemon_ServiceAbstract
     */
    public static function createService($className) {
        $config = [];
        //get last suffix class
        $serviceName = static::getServiceName($className);
        $pidFile = CDaemon_Helper::getPidFile($className);
        $logFile = CDaemon_Helper::getLogFile($className);
        $dirPidFile = dirname($pidFile);
        if (!CFile::isDirectory($dirPidFile)) {
            CFile::makeDirectory($dirPidFile, 0755, true);
        }
        $dirLogFile = dirname($logFile);
        if (!CFile::isDirectory($dirLogFile)) {
            CFile::makeDirectory($dirLogFile, 0755, true);
        }

        $config['pidFile'] = $pidFile;
        $config['logFile'] = $logFile;
        $config['serviceClass'] = $className;
        //get last suffix class
        $serviceName = static::getServiceName($className);
        $config['serviceName'] = $serviceName;
        $config['pidFile'] = $pidFile;
        $config['logFile'] = $logFile;
        $config['stdout'] = false;
        $service = new $className($serviceName, $config);

        return $service;
    }
}

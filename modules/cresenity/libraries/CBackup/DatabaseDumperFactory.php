<?php

class CBackup_DatabaseDumperFactory {
    protected static $custom = [];

    public static function createFromConnection($dbConnectionName) {
        $dbConfig = $dbConnectionName;
        if (!is_array($dbConfig)) {
            $dbConfig = CF::config('database.' . $dbConnectionName);
        }
        if (!is_array($dbConfig)) {
            throw CBackup_Exception_CannotCreateDatabaseDumperException::unsupportedDriver($dbConnectionName);
        }

        if (isset($dbConfig['read'])) {
            $dbConfig = carr::except(
                array_merge($dbConfig, $dbConfig['read']),
                ['read', 'write']
            );
        }
        $connection = carr::get($dbConfig, 'connection');
        $driver = carr::get($connection, 'type');
        $dbName = carr::get($connection, 'database');
        $username = carr::get($connection, 'user');
        $password = carr::get($connection, 'pass');
        $port = carr::get($connection, 'port');
        $host = carr::first(carr::wrap(carr::get($connection, 'host', '')));

        $dbDumper = static::forDriver($driver)
            ->setHost($host)
            ->setDbName($dbName)
            ->setUserName($username)
            ->setPassword($password);
        if ($dbDumper instanceof CBackup_Database_Dumper_MySqlDumper) {
            $dbDumper->setDefaultCharacterSet(carr::get($dbConfig, 'character_set', ''));
        }
        if ($dbDumper instanceof CBackup_Database_Dumper_MongoDbDumper) {
            //$dsn = sprintf('mongodb://%s:%s@%s:%s',$username,$password,$host,$port);
            //$mongodbUserAuth = $dsn;
            $dbDumper->setAuthenticationDatabase('admin');
        }
        if (isset($dbConfig['port'])) {
            $dbDumper = $dbDumper->setPort($dbConfig['port']);
        }
        if (isset($dbConfig['dump'])) {
            $dbDumper = static::processExtraDumpParameters($dbConfig['dump'], $dbDumper);
        }
        return $dbDumper;
    }

    public static function extend($driver, $callback) {
        static::$custom[$driver] = $callback;
    }

    protected static function forDriver($dbDriver) {
        $driver = strtolower($dbDriver);
        if (isset(static::$custom[$driver])) {
            $customDriver = static::$custom[$driver];
            return $customDriver();
        }
        if ($driver === 'mysql' || $driver === 'mysqli' || $driver === 'mariadb') {
            return new CBackup_Database_Dumper_MySqlDumper();
        }
        if ($driver === 'pgsql') {
            return new CBackup_Database_Dumper_PostgreSqlDumper();
        }
        if ($driver === 'sqlite') {
            return new CBackup_Database_Dumper_SqliteDumper();
        }
        if ($driver === 'mongodb') {
            return new CBackup_Database_Dumper_MongoDbDumper();
        }
        throw CBackup_Exception_CannotCreateDatabaseDumperException::unsupportedDriver($driver);
    }

    protected static function processExtraDumpParameters(array $dumpConfiguration, CBackup_Database_AbstractDumper $dbDumper) {
        c::collect($dumpConfiguration)->each(function ($configValue, $configName) use ($dbDumper) {
            $methodName = lcfirst(cstr::studly(is_numeric($configName) ? $configValue : $configName));
            $methodValue = is_numeric($configName) ? null : $configValue;
            $methodName = static::determineValidMethodName($dbDumper, $methodName);
            if (method_exists($dbDumper, $methodName)) {
                static::callMethodOnDumper($dbDumper, $methodName, $methodValue);
            }
        });
        return $dbDumper;
    }

    protected static function callMethodOnDumper(CBackup_Database_AbstractDumper $dbDumper, $methodName, $methodValue) {
        if (!$methodValue) {
            $dbDumper->$methodName();
            return $dbDumper;
        }
        $dbDumper->$methodName($methodValue);
        return $dbDumper;
    }

    protected static function determineValidMethodName(CBackup_Database_AbstractDumper $dbDumper, $methodName) {
        return c::collect([$methodName, 'set' . ucfirst($methodName)])
                        ->first(function ($methodName) use ($dbDumper) {
                            return method_exists($dbDumper, $methodName);
                        }, '');
    }
}

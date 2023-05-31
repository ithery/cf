<?php

class CDatabase_Config {
    /**
     * @param string|array $name
     *
     * @return null|array
     */
    public static function resolve($name) {
        $config = null;
        if (is_string($name)) {
            //maybe url or connection name
            if (strpos('://', $name) !== false) {
                //this is dsn or url
                $config = static::dsnToArray($name);
            } else {
                //we will try to resolve the config name
                $config = CDatabase::manager()->getConfig('connections.' . $name);
                if ($config === null) {
                    //we will try to resolve the first array of config
                    $config = CDatabase::manager()->getConfig($name);
                }
                if (!is_null($config)) {
                    if (is_string($config)) {
                        //if config is string we will try to resolve back
                        static::resolve($config);
                    }
                }
            }
        }

        if (is_array($name)) {
            $config = $name;
        }

        if (is_array($config)) {
            //we will try to flatten format this config
            return static::flattenFormat($config);
        }

        return null;
    }

    public static function flattenFormat(array $config) {
        if (isset($config['connection'])) {
            $connection = $config['connection'];
            $formattedConnection = static::reformatConnectionFormat($connection);
            //this is old format, we will reformat this to new format

            $config = array_merge($formattedConnection, $config);
        }

        if (isset($config['user'])) {
            if (!isset($config['username'])) {
                $config['username'] = $config['user'];
            }
            unset($config['user']);
        }

        if (isset($config['pass'])) {
            if (!isset($config['password'])) {
                $config['password'] = $config['pass'];
            }
            unset($config['pass']);
        }
        if (isset($config['character_set'])) {
            if (!isset($config['charset'])) {
                $config['charset'] = $config['character_set'];
            }
            unset($config['character_set']);
        }
        if (isset($config['table_prefix'])) {
            $config['prefix'] = $config['table_prefix'];
            unset($config['table_prefix']);
        }
        if (!isset($config['collation'])) {
            $config['collation'] = null;
            if (carr::get($config, 'charset') == 'utf8mb4') {
                $config['collation'] = 'utf8mb4_unicode_ci';
            }
        }
        if (isset($config['socket'])) {
            if (!isset($config['unix_socket'])) {
                $config['unix_socket'] = $config['socket'];
            }
            unset($config['socket']);
        }
        if (isset($config['type'])) {
            if (!isset($config['driver'])) {
                $config['driver'] = $config['type'];
            }
            unset($config['type']);
        }

        if (isset($config['escape'])) {
            //deprecated for escape, we will always escape all values on database
            unset($config['escape']);
        }

        if (isset($config['object'])) {
            //deprecated for object, we will always fetch object for all fetching database
            unset($config['object']);
        }
        if (isset($config['persistent'])) {
            //deprecated for persistent
            unset($config['persistent']);
        }

        // $defaultConfig = [
        //     'benchmark' => true,
        //     'persistent' => false,
        //     'connection' => '',
        //     'charset' => 'utf8mb4',
        //     'collation' => 'utf8mb4_unicode_ci',
        //     'prefix' => '',
        //     'object' => true,
        //     'cache' => false,
        //     'escape' => true,
        // ];

        // $config = array_merge($defaultConfig, $config);

        if (!isset($config['port']) || $config['port'] == false) {
            if (isset($config['driver'])) {
                $port = static::getDefaultPort($config['driver']);
                if ($port !== null) {
                    $config['port'] = $port;
                }
            }
        }

        return $config;
    }

    protected static function normalizeDriver($driver) {
        $mappedDriver = [
            'mysqli' => 'mysql'
        ];

        return carr::get($mappedDriver, $driver, $driver);
    }

    protected static function getDefaultPort($driver) {
        $mappedPort = [
            'mysql' => '3306'
        ];

        return carr::get($mappedPort, static::normalizeDriver($driver));
    }

    protected static function reformatConnectionFormat($connection) {
        $config = [];

        $config['driver'] = carr::get($connection, 'type');
        $config['username'] = carr::get($connection, 'user');
        $config['password'] = carr::get($connection, 'pass');
        $config['port'] = carr::get($connection, 'port');
        $config['host'] = carr::get($connection, 'host');
        $config['unix_socket'] = carr::get($connection, 'socket');
        $config['database'] = carr::get($connection, 'database');
        $config['dsn'] = carr::get($connection, 'dsn');

        return $config;
    }

    protected static function dsnToArray($dsn) {
        return  (new CDatabase_ConfigurationUrlParser())->parseConfiguration($dsn);
    }
}

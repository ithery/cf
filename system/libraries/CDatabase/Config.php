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
                $config = CF::config('database.connections.' . $name);
                if ($config === null) {
                    //we will try to resolve the first array of config
                    $config = CF::config('database.' . $name);
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

    protected static function flattenFormat(array $config) {
        if (isset($config['connection'])) {
            $connection = $config['connection'];
            $formattedConnection = static::reformatConnectionFormat($connection);
            //this is old format, we will reformat this to new format

            $config = array_merge($formattedConnection, $config);
        }

        if (isset($config['character_set'])) {
            $config['charset'] = $config['character_set'];
            unset($config['character_set']);
        }
        if (isset($config['table_prefix'])) {
            $config['prefix'] = $config['table_prefix'];
            unset($config['table_prefix']);
        }
        if (!isset($config['collation'])) {
            $config['collation'] = null;
        }

        $defaultConfig = [
            'benchmark' => true,
            'persistent' => false,
            'connection' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'object' => true,
            'cache' => false,
            'escape' => true,
        ];

        return array_merge($defaultConfig, $config);
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

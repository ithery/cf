<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Session cache driver.
 *
 * Cache library config goes in the session.storage config entry:
 * $config['storage'] = array(
 *     'driver' => 'apc',
 *     'requests' => 10000
 * );
 * Lifetime does not need to be set as it is
 * overridden by the session expiration setting.
 */
class CSession_Driver_Cache implements CSession_Driver {
    protected $cache;
    protected $encrypt;

    public function __construct() {
        // Load Encrypt library
        if (CF::config('session.encryption')) {
            $this->encrypt = new Encrypt;
        }

        CF::log(CLogger::DEBUG, 'Session Cache Driver Initialized');
    }

    public function open($path, $name) {
        $config = CF::config('session.storage');

        if (empty($config)) {
            // Load the default group
            $config = CF::config('cache.default');
        } elseif (is_string($config)) {
            $name = $config;

            // Test the config group name
            if (($config = CF::config('cache.' . $config)) === null) {
                throw new CException('The :name group is not defined in your configuration.', [':name' => $name]);
            }
        }

        $config['lifetime'] = (CF::config('session.expiration') == 0) ? 86400 : CF::config('session.expiration');
        $this->cache = new Cache($config);

        return is_object($this->cache);
    }

    public function close() {
        return true;
    }

    public function read($id) {
        $id = 'session_' . $id;
        if ($data = $this->cache->get($id)) {
            return CF::config('session.encryption') ? $this->encrypt->decode($data) : $data;
        }

        // Return value must be string, NOT a boolean
        return '';
    }

    public function write($id, $data) {
        $id = 'session_' . $id;
        $data = CF::config('session.encryption') ? $this->encrypt->encode($data) : $data;

        return $this->cache->set($id, $data);
    }

    public function destroy($id) {
        $id = 'session_' . $id;
        return $this->cache->delete($id);
    }

    public function regenerate() {
        session_regenerate_id(true);

        // Return new session id
        return session_id();
    }

    public function gc($maxlifetime) {
        // Just return, caches are automatically cleaned up
        return true;
    }
}

// End Session Cache Driver

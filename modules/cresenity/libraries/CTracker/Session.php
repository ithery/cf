<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:51:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Session {

    public function get($key) {
        $session = $this->getNamespaceData();
        return carr::get($session, $key);
    }

    public function has($key) {
        $session = $this->getNamespaceData();
        return isset($session[$key]);
    }

    public function set($key, $value) {
        $session = $this->getNamespaceData();
        if (!is_array($session)) {
            $session = array();
        }

        $session[$key] = $value;
        
        
        $this->setNamespaceData($session);
    }  
    /**
     * Alias of function set
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function put($key, $value) {
        return $this->set($key, $value);
    }

    protected function getNamespaceData() {
        return CSession::instance()->get(self::sessionNamespace(), array());
    }

    protected function setNamespaceData($value) {
         
        return CSession::instance()->set(self::sessionNamespace(), $value);
    }

    public static function sessionNamespace() {
        return CTracker::config()->get('sessionNamespace', 'CTracker');
    }

}

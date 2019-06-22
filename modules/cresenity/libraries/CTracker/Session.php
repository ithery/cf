<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 1:51:48 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CTracker_Session {

    const SESSION_NAMESPACE = 'CTracker';

    public function get($key, $namespace = null) {
        $session = $this->getNamespaceData($namespace);
        return carr::get($session, $key);
    }

    public function has($key, $namespace = null) {
        $session = $this->getNamespaceData($namespace);
        return isset($session[$key]);
    }

    public function set($key, $value) {
        $session = $this->getNamespaceData();
        $session[$key] = $value;
        $this->setNamespaceData($namespace, $session);
    }

    private function getNamespaceData() {
        return CSession::instance()->get(self::SESSION_NAMESPACE, array());
    }

    private function setNamespaceData($value) {
        return CSession::instance()->set(self::SESSION_NAMESPACE, $value);
    }

}

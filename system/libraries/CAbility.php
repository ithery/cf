<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAbility {
    protected static $instances = [];

    protected $serverRemoteId;

    protected $data;

    /**
     * @param mixed $serverRemoteId
     *
     * @return CAbility
     *
     * @throws CException
     */
    public static function &instance($serverRemoteId) {
        if (!isset(CAbility::$instances[$serverRemoteId])) {
            // Create a new instance
            CAbility::$instances[$serverRemoteId] = new CAbility($serverRemoteId);
        }

        return CAbility::$instances[$serverRemoteId];
    }

    protected function __construct($serverRemoteId) {
        $this->serverRemoteId = $serverRemoteId;
        $this->refresh();
    }

    protected function refresh() {
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    public function getData($key = '') {
        $data = $this->data;
        if ($key) {
            $data = c::get($this->data, $key, []);
        }
        return $data;
    }

    public function getExist($key) {
        $data = c::get($this->data, $key, []);
        return c::get($data, 'exist', false);
    }
}

<?php
class CApi_Session {
    protected $sessionId;

    protected $data;

    protected $apiGroup;
    protected $driver = false;

    public function __construct(CApi_Session_DriverAbstract $driver, $sessionId) {
        $this->driver = $driver;
        //set basePath if not set in child class
        $this->sessionId = $sessionId;

        if (!$this->driver->exists($sessionId)) {
            throw new CApi_Exception('sessionId ' . $sessionId . 'not found');
        }
        $this->load();
    }

    public function get($key) {
        return carr::get($this->data, $key);
    }

    public function data() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
        $this->save();
        return $this;
    }

    public function set($key, $val, $save = true) {

        $this->data[$key] = $val;
        if ($save) {
            $this->save();
        }
        return $this;
    }

    public function save() {
        return $this->driver->write($this->sessionId, $this->data);
    }

    public function exists() {
        return strlen(carr::get($this->data, 'sessionId')) > 0;
    }

    public function load() {
        $this->data = $this->driver->read($this->sessionId);

        return $this;
    }

    public function sessionId() {
        return $this->sessionId;
    }

    public function __destruct() {
        $this->save();
    }

    public function driver() {
        return $this->driver;
    }
}

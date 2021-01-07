<?php

class CApi_Session_Driver_NullDriver extends CApi_Session_DriverAbstract {
    public function close() {
        return true;
    }

    public function destroy($id) {
        return true;
    }

    public function gc($maxlifetime) {
        return true;
    }

    public function exists($id) {
        return true;
    }

    public function read($id) {
        return [];
    }

    public function regenerate() {
    }

    public function write($id, $data) {
        return true;
    }
}

<?php

class CPHPInfo {

    private static $_instance = null;
    private $_info = array();

    public function __construct() {


        $this->_info = CServer::phpInfo()->get();
    }

    public static function instance() {
        if (self::$_instance == null) {
            self::$_instance = new CPHPInfo();
        }
        return self::$_instance;
    }

    public function get_array() {
        return $this->_info;
    }

    public function system() {
        if (isset($this->_info['phpinfo']['System']))
            return $this->_info['phpinfo']['System'];
        return false;
    }

    public function server_api() {
        if (isset($this->_info['phpinfo']['Server API']))
            return $this->_info['phpinfo']['Server API'];
        return false;
    }

    public function loaded_configuration_file() {
        if (isset($this->_info['phpinfo']['Loaded Configuration File']))
            return $this->_info['phpinfo']['Loaded Configuration File'];
        return false;
    }

}

?>
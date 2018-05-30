<?php

defined('SYSPATH') OR die('No direct access allowed.');

class Capp_Controller extends CController {

    public function index() {
        
    }

    
    public function phpinfo() {
        phpinfo();
    }

    public function info() {
        $data = array();
        $data['org_id'] = CF::org_id();
        $data['org_code'] = CF::org_code();
        $data['app_id'] = CF::app_id();
        $data['app_code'] = CF::app_code();
        $data['store_id'] = CF::store_id();
        $data['store_code'] = CF::store_code();
        $data['domain'] = CF::domain();


        echo cjson::encode($data);
    }

}

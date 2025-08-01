<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Remote_Client_Engine_App extends CApp_Remote_Client_Engine {
    public function __construct($options) {
        parent::__construct($options);
        $this->baseApiUrl .= 'App/';
    }

    public function getConfig($domain) {
        $post = [];
        $post['domain'] = $domain;
        $data = $this->request($this->baseApiUrl . 'GetConfig', $post);

        return $data;
    }
}

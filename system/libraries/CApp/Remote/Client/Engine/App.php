<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Remote_Client_Engine_App extends CApp_Remote_Client_Engine {
    /**
     * @param array $options
     */
    public function __construct($options) {
        parent::__construct($options);
        $this->baseApiUrl .= 'App/';
    }

    /**
     * @param string $domain
     *
     * @return mixed
     */
    public function getConfig($domain) {
        $post = [];
        $post['domain'] = $domain;
        $data = $this->request($this->baseApiUrl . 'GetConfig', $post);

        return $data;
    }
}

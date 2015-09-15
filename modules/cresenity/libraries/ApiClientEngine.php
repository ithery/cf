<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 20, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiClientEngine extends ApiEngine {
        
        protected $api_client = null;
        
        protected function __construct($api_client) {
            parent::__construct();
            
            $this->session = null;
            
            $this->api_client = $api_client;
            
            if ($this->api_client instanceof ApiClient) {
                $this->request = $this->api_client->get_request();
                $this->session = $this->api_client->get_session();
            }
            else {
                throw new Exception($this->api_client ." must be instance of ApiClient");
            }
        }
        
        protected function error() {
            return ApiError::instance();
        }
        
        public function get_api_client(){
            return $this->api_client;
        }
        
        public function get_session(){
            return $this->session;
        }
    }
    
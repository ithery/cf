<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 4, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiServerEngine extends ApiEngine {
        
        protected $api_server = null;
        protected $request;
        protected $response;
        protected $session;

        protected function __construct($api_server){
            parent::__construct();
            
            $this->session = null;
            $this->api_server = $api_server;
            if ($this->api_server instanceof ApiServer) {
                $this->request = $this->api_server->get_request();
            }
            else {
                throw new Exception($this->api_server ." must be instance of ApiServer");
            }
        }
        
        
        public function error(){
            return ApiError::instance();
        }
        
        public function set_session($session_class) {
            if ($session_class instanceof ApiSession) {
                $this->session = $session_class;
            }
            else {
                throw new Exception("Session is not instanceof ApiSession");
            }
            return $this;
        }
       
        public function get_api_server(){
            return $this->api_server;
        }
        
        public function get_session(){
            return $this->session;
        }
        
    }
    
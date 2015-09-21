<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 19, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiClient extends Api {
        
        protected $url;
        protected $request;
        protected $req_args;
        protected $service_name = null;
        protected $api_client_engine = null;
        protected $session_id = null;
        protected $session = null;
        protected static $instance = null;
        
        protected function __construct($args) {
            parent::__construct($args);
            
            $this->req_args = $args;
            $this->request['GET'] = null;
            $this->request['POST'] = null;
            
             // create object error
            ApiError::instance();
        }
        
        final public function __before(){
            $this->before();
        }
        
        final public function __process() {
            if ($this->api_client_engine != null) {
                
                if ($this->api_client_engine instanceof ApiEngine) {
                    try {
                        $server_response = $this->api_client_engine->exec();
                        $this->response = $server_response;
                    }
                    catch (Exception $exc) {
                        throw $exc;
                    }

                }
            }
        }
        
        final public function __after(){
            $this->session_id = $this->session->get_session_id();
            
            $this->after();

            $response = array();
            $response['err_code'] = $this->error()->code();
            $response['err_message'] = $this->error()->get_err_message();
            if ($this->error()->code() == 0) {
                $response['data'] = carr::get($this->response, 'data');
            }
            else {
                $response['data'] = array();
            }
            
            $this->response = $response;
        }
        
        
        final public function exec($session_id = null) {
            $this->session_id = $session_id;
            
            $this->__before();
            
            $this->__process();
            
            $this->__after();
            
            return $this->response;
        }
        
        public function before() {
            
        }
        
        public function after() {
            
        }
        
        public function set_get_data($get_data){
            $this->request['GET'] = $get_data;
            return $this;
        }
        
        public function set_post_data($post_data){
            $this->request['POST'] = $post_data;
            return $this;
        }
        
        public function set_url($url){
            $this->url = $url;
            return $this;
        }
        
        protected function error() {
            return ApiError::instance();
        }
        
        public function get_service_name(){
            return $this->service_name;
        }
        
        public function get_session() {
            return $this->session;
        }
        
        public function get_session_id() {
            return $this->session_id;
        }
    }
    
<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 3, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiServer extends Api {

        public static $has_return = false;
        protected static $instance = null;

        /**
         * 
         * @var Array Arguments
         */
        protected $total_args = 0;

        /**
         *
         * @var Array Data Type: array, xml, json  
         */
        protected $accept_data_type = array();
        protected $default_data_type = 'array';
        protected $req_args;
        protected $req_headers = array();
        protected $current_service = null;
        protected $current_service_name = null;
        protected $class_service = null;
        protected $table_prefix = '';
        protected $client_log_id = null;
        protected $related_log_path = '';
        protected $full_log_path = '';

        /**
         *
         * @var ApiSession 
         */
        protected $session = null;

        protected function __construct($args) {
            parent::__construct($args);

            $this->req_args = $args;
            $this->table_prefix = '';
            CFBenchmark::start('client_activity');

            // create object error
            ApiError::instance();
        }

        final public function register_service($service_name) {
            // set type of service_name is array
            if (is_string($service_name)) {
                $service_name = array($service_name);
            }

            if (is_array($service_name)) {
                foreach ($service_name as $service_name_k => $service_name_v) {
                    $this->service[$service_name_k] = $service_name_v;
                }
            }

            return $this;
        }

        /**
         * @final
         */
        final public function __before($other_request) {
            $db = CDatabase::instance();

            // validation args
            if (count($this->req_args) != $this->total_args) {
                // error, go to hell
                $this->error()->add_default(1001);
            }
            $this->req_headers['ip_address'] = crequest::remote_address();

            // Get all request based on method.
            $request = array();

            $direct_access = false;
            if (is_array($other_request) && count($other_request) > 0) {
                $request += $other_request;
                $direct_access = true;
            }
            $get_data=null;
            $post_data=null;
            switch ($this->method) {
                case self::__GET:
                    if ($direct_access == true) {
                        $get_data = carr::get($other_request, 'GET');
                    }
                    else {
                        $get_data = $_GET;
                    }
                    $request += $get_data;
                    break;
                case self::__POST:
                    if ($direct_access == true) {
                        $post_data = carr::get($other_request, 'POST');
                    }
                    else {
                        $post_data = $_POST;
                    }
                    $request += $post_data;
                    break;
                case self::__GET_POST:
                    if ($direct_access == true) {
                        $get_data = carr::get($other_request, 'GET');
                        $post_data = carr::get($other_request, 'POST');
                    }
                    else {
                        // default get raw post data;
                        $post_data = file_get_contents("php://input");
                        $get_data = $_GET;
                    }
                    if (!is_array($get_data)) {
                        $get_data = array($get_data);
                    }
                    $request += $get_data;
                    // validate data_type
                    $data_type_valid = false;
                    $data_type_request = carr::get($request, 'data_type', $this->default_data_type);
                    $data_type_request = strtolower($data_type_request);
                    foreach ($this->accept_data_type as $data_type_k => $data_type_v) {
                        if ($data_type_v == $data_type_request) {
                            $data_type_valid = true;
                        }
                    }

                    if ($data_type_valid == false) {
                        // error data type invalid
                    }

                    if ($data_type_valid == true) {
                        switch ($data_type_request) {
                            case 'array':
                                $request += $_POST;
                                break;
                            case 'xml':
                                // get all xml request
                                $xml_obj = simplexml_load_string($post_data);
                                $request = $this->xml2array($xml_obj);
                                break;
                            default:
                                $request += $post_data;
                                break;
                        }
                    }

                    break;
                default:
                    // error method doesnt support
                    $post_data = null;
                    $get_data = null;
                    break;
            }
//            cdbg::var_dump($request);
            $this->request = $request;

            $this->before();

            // validation service called is registered.
            if ($this->error()->code() == 0) {
                $service_found = false;
                foreach ($this->service as $service_k => $service_v) {
                    if ($service_k == $this->current_service_name) {
                        $service_found = true;
                        $this->current_service = $service_v;
                        break;
                    }
                }
                if ($service_found == false) {
                    $this->error()->add_default(1004);
                }
            }

            if ($this->generate_doc == false) {
                if ($this->validate == true) {
                    //Validasi Input
                    try {
                        Helpers_Validation_Api::validate($this->service[$this->current_service_name], $this->request, 2);
                    }
                    catch (Helpers_Validation_Api_Exception $e) {
                        $this->error()->add_default(12999, $e->getMessage());
                    }

                    if ($this->session instanceof ApiSession) {
                        $this->session->set('request_' . $this->current_service_name, $this->request);
                    }
                }
            }

            // validation request with current service config
            $service_input = carr::get($this->current_service, 'input');

            $this->log_request($get_data, $post_data);
        }

        protected function log_request($get_data, $post_data) {
            $session_id = null;
            if ($this->session instanceof ApiSession) {
                $session_id = $this->session->get_session_id();
            }

            // do log request from client - create file log request at related folders
            $file_name = 'CLIENT_';
            if ($session_id == null) {
                $file_name = 'CLIENT_' . 'DEF';
            }
            $file_name .= "_" . date("His") . '_' . $this->current_service_name . "_"
                    . mt_rand(10000, 99999) . '_rq.log';

            $api_name = 'api_multi';
            if (property_exists($this, 'api_name')) {
                $api_name = $this->api_name;
            }
            $log_path = 'logs' . DS . $api_name . DS . 'server' . DS .date('Ymd').DS.date('H').DS. $this->related_log_path;
            
            $full_log_path = CAPPPATH;

            $full_log_path = str_replace('//', DS, $full_log_path);
            $temp_log_path = explode(DS, $log_path);
            foreach ($temp_log_path as $k => $v) {
                $full_log_path .= $v . DS;
                if (!is_dir($full_log_path) && !file_exists($full_log_path)) {
                    @mkdir($full_log_path);
                }
            }
            $this->full_log_path = $full_log_path;
            if ($this->session instanceof ApiSession) {
                $this->session->set('client_full_log_path', $this->full_log_path);
            }
            
            $full_log_path .= $file_name;
            $data_log_file = array(
                'service_name' => $this->current_service_name,
                'request' => $this->request,
                'get_data' => $get_data,
                'post_data' => $post_data,
            );
            @file_put_contents($full_log_path, json_encode($data_log_file));

            if (strlen($this->table_prefix) > 0) {
                // do log request from client - insert into client_log_request
                $db = CDatabase::instance();
                $data = array(
                    'org_id' => null,
                    'product_category_code' => null,
                    'product_code' => null,
                    'request_date' => date("Y-m-d H:i:s"),
                    'url' => curl::httpbase(),
                    'auth' => NULL,
                    'session_id' => $session_id,
                    'service_name' => $this->current_service_name,
                    'request' => $full_log_path,
                    'request_path' => CAPPPATH,
                    'response' => '',
                    'response_path' => CAPPPATH,
                    'http_response_code' => null,
                    'execution_time' => null,
                    'ip_address' => $this->get_req_ip_address()
                );
//                $db->insert($this->table_prefix . '_client_log_request', $data);
//                $this->client_log_id = $db->insert_id();
            }
        }

        /**
         * 
         * @throws Exception
         */
        final private function __process() {
            if ($this->class_service != null) {

                if ($this->class_service instanceof ApiEngine) {
                    $class_service = $this->class_service;

                    try {
                        // call service at Class of Service.
                        $service_response = $class_service->exec();
                        $this->response = $service_response;
                    }
                    catch (Exception $exc) {
                        throw $exc;
                    }
                }
                else {
                    throw new Exception("Class service must be instance of ApiService");
                }
            }
            else {
                throw new Exception("Class Service cant be null");
            }
        }

        /**
         * @final
         */
        final public function __after() {
            $this->after();
            
            if ($this->session instanceof ApiSession) {
                $this->session->set('response_' . $this->current_service_name, $this->response);
            }

            $response = array();
            $response['err_code'] = $this->error()->code();
            $response['err_message'] = $this->error()->get_err_message();
            if ($this->error()->code() == 0) {
                $response_error = carr::get($this->response, 'err_code');
                $response_error_message = carr::get($this->response, 'err_message');
                $response['data'] = carr::get($this->response, 'data');
                if ($response_error > 0) {
                    $response['err_code'] = $response_error;
                    $response['err_message'] = $response_error_message;
                    //$response['data'] = array();
                }
            }
            else {
                //$response['data'] = array();
            }

            $benchmark = CFBenchmark::get('client_activity');
            $execution_time = 0;
            if (isset($benchmark['time'])) {
                $execution_time = $benchmark['time'];
            }
            // do log response to client
            // parsing response to client based on format response
            $session_id = null;
            if ($this->session instanceof ApiSession) {
                $session_id = $this->session->get_session_id();
            }

            $file_name = 'CLIENT_' . $session_id;
            if ($session_id == null) {
                $file_name = 'CLIENT_' . 'DEF';
            }
            $file_name .= "_" . date("His") . '_' . $this->current_service_name . "_"
                    . mt_rand(10000, 99999) . '_rs.log';
            $this->full_log_path .= $file_name;
            $data_log_file = array(
                'service_name' => $this->current_service_name,
                'response' => $response,
            );
            file_put_contents($this->full_log_path, json_encode($data_log_file));

            $this->response = $response;
            if (self::$has_return == false) {
                echo cjson::encode($response);
            }
        }

        /**
         * 
         * @final
         * @throws Exception
         */
        public function exec($other_request = array()) {
            $this->__before($other_request);

            if ($this->generate_doc == false) {
                if ($this->error()->code() == 0) {
                    try {
                        $this->__process();
                    }
                    catch (Exception $exc) {
                        throw $exc;
                    }
                }
            }
            if ($this->generate_doc == false) {
                $this->__after();
                
            }
            if ($this->generate_doc == true || self::$has_return == true) {
                return $this;
            }
        }

        /**
         *  This function just for ignore undefined function.
         *  Override this function to do something with API.
         */
        protected function before() {
            
        }

        protected function after() {
            
        }

        protected function error() {
            return ApiError::instance();
        }

        public function get_req_args() {
            return $this->req_args;
        }

        public function get_current_service() {
            return $this->current_service;
        }

        public function get_current_service_name() {
            return $this->current_service_name;
        }

        public function set_class_service($class_service) {
            $this->class_service = $class_service;
            return $this;
        }

        public function get_req_headers() {
            return $this->req_headers;
        }

        public function get_req_ip_address() {
            return $this->req_headers['ip_address'];
        }

        public function set_has_return($bool) {
            self::$has_return = $bool;
            return $this;
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

    }
    
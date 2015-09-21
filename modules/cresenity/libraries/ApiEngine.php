<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 20, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiEngine {

        /**
         *
         * @var String 
         */
        protected $url_request;

        /**
         *
         * @var CCurlMulti 
         */
        protected $curl_multi;

        /**
         *
         * @var CCurl 
         */
        protected $curl;
        protected $request;
        protected $response;
        protected $session;
        protected $related_curl_log_path = "";
        protected $prefix_log_file_name = null;

        protected function __construct() {
            
        }

        public function exec($curr_service) {
            if ($this->error()->code() == 0) {
                if (method_exists($this, $curr_service)) {
                    $this->response = $this->$curr_service();
                }
                else {
                    throw new Exception("Service " . $curr_service . " is not declared");
                }
                return $this->response;
            }
        }

        public function send_request($service_name, $data = null, $exec = false, $obj_callback = null) {
            $curl = CCurl::factory(null);
            $curl->set_timeout(40000);
            $curl->set_useragent('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0');

            if ($data != null) {
                if (is_array($data)) {
                    $curl->set_post($data);
                }
                else {
                    $curl->set_raw_post($data);
                }
            }
            $curl->set_opt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->set_opt(CURLOPT_SSL_VERIFYHOST, 2);
            $curl->set_opt(CURLOPT_ENCODING, 'gzip, deflate');

            $this->request_callback($curl, $service_name, $data);
            if ($obj_callback != null) {
                $obj_callback->request_callback($curl, $service_name, $data);
            }

            $session_id = $this->session->get('session_id');

            $curl->set_url($this->url_request);
            if ($exec == true) {
                // log request to server
                // WRITE LOG REQUEST
                $this->__write_log($service_name, 'request');

                // execute curl
                $curl->exec();

                // get error
                $has_error = $curl->has_error();

                // get response
                $response = $curl->response();
                $this->response = $response;

//            var_dump($this->response);
                // log response from server
                // WRITE LOG RESPONSE
                $this->__write_log($service_name, 'response');

                // get http response code
                $http_response_code = $curl->get_http_code();
            }

            if ($exec == false) {
                return $curl;
            }
            return $this;
        }

        protected function request_callback() {
            
        }

        public function get_response() {
            return $this->response;
        }

        /**
         * 
         * @param String $service_name      Service name must be unique
         * @param String $data              Data can be String / Array
         * @param Object $obj_callback      Object that have function request_callback
         * @return \CApiMultiEngine
         */
        public function add_curl($service_name, $data = null, $obj_callback = null) {
            $this->curl[$service_name] = $this->send_request($service_name, $data, false, $obj_callback);
            return $this;
        }

        public function send_multi_request($service_name_multi) {
            $this->curl_multi = CCurlMulti::factory();

            $request_data = array();
            $req = array();
            foreach ($this->curl as $curl_k => $curl_v) {
                $this->curl_multi->add_curl($curl_k, $curl_v);
                $arr_req = $curl_v->get_post_data();
                $req[$curl_k]['post_data'] = $arr_req;
            }
            $request_data[$service_name_multi] = $req;
            $this->__write_log($service_name_multi, 'request', json_encode($request_data));
//            $a = $this->session->get('product_category_code');
//            $b = $this->session->get('session_id');
//        $this->__write_log($service_name_multi, 'request');
            // execute all curl
            $this->curl_multi->exec();

            // get response from curl multi
            $response = $this->curl_multi->last_response();
            $this->__write_log($service_name_multi, 'response', json_encode($response));
            return $response;
        }

        public function clear_curl() {
            $this->curl = array();
        }

        public function set_url_request($url_request) {
            $this->url_request = $url_request;
            return $this;
        }

        public function get_curl() {
            return $this->curl;
        }

        private function __write_log($service_name, $status, $log_data = null) {
            $session_id = $this->session->get('session_id');

            $status_log = 'rq';
            if (strlen($status) > 0) {
                if ($status == 'request') {
                    $status_log = 'rq';
                    if ($log_data == null) {
                        $data_log = array(
                            'URL' => $this->url_request,
                            'get_data' => $this->request['GET'],
                            'post_data' => $this->request['POST'],
                        );
                    }
                    else {
                        $data_log = $log_data;
                    }
                }
                if ($status == 'response') {
                    $status_log = 'rs';
                    if ($log_data == null) {
                        $data_log = $this->response;
                    }
                    else {
                        $data_log = $log_data;
                    }
                }
            }
//        cdbg::var_dump($data_log);
            $log_path = 'logs' . DS;
            $log_path .= $this->related_curl_log_path;


            $full_log_path = CAPPPATH;

            $full_log_path = str_replace('//', DS, $full_log_path);
            $temp_log_path = explode(DS, $log_path);

            foreach ($temp_log_path as $k => $v) {
                $full_log_path .= $v . DS;
                if (!is_dir($full_log_path)) {
                    mkdir($full_log_path);
                }
            }


            if (strlen($this->prefix_log_file_name) == 0) {
                $this->prefix_log_file_name = 'CLIENT_';
            }

            $prefix_file_name = $this->prefix_log_file_name . $session_id;
            if ($session_id == null) {
                $prefix_file_name = $this->prefix_log_file_name . 'DEF';
            }

            $filename = $prefix_file_name . '_' . $service_name . "_" . mt_rand(10000, 99999) . "_" . date("His") . '_' . $status_log . '.log';

            if (is_array($data_log)) {
                $data_log = json_encode($data_log);
            }

//        var_dump($full_log_path .$filename);
            file_put_contents($full_log_path . $filename, $data_log);

            return $log_path;
        }

        public function set_prefix_log_file_name($prefix) {
            $this->prefix_log_file_name = $prefix;
            return $this;
        }

    }
    
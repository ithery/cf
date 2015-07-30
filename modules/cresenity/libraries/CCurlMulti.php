<?php

    /*
     * Description of ThemeparkEngine
     * @author Joko Jainul A
     * @since Jul 23, 2015 10:13:00 AM
     */
    
    class CCurlMulti {

        private $url;
        private $engine;
//        private $options;
//        private $curl;
        private $data;
//        private $last_status;
//        private $last_followed;
//        private $last_caseless;


        public function __construct($url = NULL, $data = NULL, $engine = 'curl') {
            $this->url = $url;
            $this->engine = $engine;
//            $this->curl = CCurl::factory(NULL);
            $this->data = $data;
//            $this->last_status = array();
        }
        
        public static function factory($url, $data, $engine) {
            return new CCurlMulti($url, $data, $engine);
        }
        
//        private function clear_last_exec() {
//            $this->last_status = array();
//            $this->last_followed = array();
//            $this->last_caseless = array();
//            $this->last_headers = array();
//            $this->last_response = null;
//            $this->last_response_body = null;
//            $this->last_response_header = null;
//        }
        
//        public function set_opt($key, $value, $overwrite = true) {
//            if (!$overwrite) {
//                if (isset($this->options[$key])) {
//                    return $this;
//                }
//            }
//            $this->options[$key] = $value;
//            return $this;
//        }
        
        
//        public function set_timeout($milisecond) {
//            $this->set_opt(CURLOPT_TIMEOUT, $milisecond);
//            return $this;
//        }
//        
//        public function set_useragent($useragent) {
//            $this->set_opt(CURLOPT_USERAGENT, $useragent);
//            return $this;
//        }
        
        public function exec() {
            
            $data = $this->data;
            
            //open curl connection
            if (!$curl->opened()) {
                $curl->open();
            }
            
            // array of curl handles
            $curly = array();
            // data to be returned
            $result = array();
            
            // multi handle
            $mh = curl_multi_init();
            
            // loop through $data and create curl handles
            // then add them to the multi-handle
            foreach ( $data as $id => $d ) {

                //$curly[$id] = curl_init();
                $curl = CCurl::factory(NULL);
                $curl->open();
                $curly[$id] = $curl->handle;
                $curl->set_timeout(40000);
                $curl->set_useragent('Mozilla/5.0 (Windows NT 6.1; WOW64; rv:26.0) Gecko/20100101 Firefox/26.0');                

                $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
                //$curl->url = $url;
//                curl_setopt($curly[$id], CURLOPT_URL, $url);
//                curl_setopt($curly[$id], CURLOPT_HEADER, 0);
//                curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
//                curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER, 0);
                $curl->set_opt(CURLOPT_URL, $url);
                $curl->set_opt(CURLOPT_HEADER, 0);
                $curl->set_opt(CURLOPT_RETURNTRANSFER, 1);
                $curl->set_opt(CURLOPT_SSL_VERIFYPEER, 0);

                // post?
                if ( is_array($d) ) {
                    if ( !empty($d['post']) ) {
                        $curl->set_post($d['data']);
//                        curl_setopt($curly[$id], CURLOPT_POST, 1);
//                        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
                    }
                }
                
                $curl->set_opt(CURLOPT_SSL_VERIFYPEER, FALSE);
                $curl->set_opt(CURLOPT_SSL_VERIFYHOST, 2);
                $curl->set_opt(CURLOPT_ENCODING, 'gzip, deflate');
                
                //set all option
                foreach ($curl->options as $k => $v) {
                    curl_setopt($curly[$id], $k, $v);
                }

                // extra options?
                if ( !empty($options) ) {
                    curl_setopt_array($curly[$id], $options);
                }

                curl_multi_add_handle($mh, $curly[$id]);
            }
            
            // execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            }
            while ( $running > 0 );

            // get content and remove handles
            foreach ( $curly as $id => $c ) {
                $result[$id] = curl_multi_getcontent($c);
                curl_multi_remove_handle($mh, $c);
            }

            // all done
            curl_multi_close($mh);

            return $this;
        }
        
//        public function has_error() {
//            if (isset($this->last_status['error'])) {
//                return (empty($this->last_status['error']) ? false : $this->last_status['error']);
//            } else {
//                return false;
//            }
//        }

    }
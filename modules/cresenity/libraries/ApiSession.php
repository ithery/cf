<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Aug 7, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiSession {

        const _digit = 4;
        protected $reserved_key = array();
        protected $validate_expired = true;
        protected $last_activity = null;
        protected $have_cookies = false;
        protected $session_id;
        protected $data;
        protected $session_path = null;
        protected $log_path = null;
        protected $cookies_path = null;
        protected $using_fopen = false;
        protected static $instance;
        protected $default_session_path = null;
        protected $default_cookies_path = null;
        protected $default_log_path = null;
        protected $encrypt = false;

        public function __construct($session_id = null, $have_cookies = false) {
            $include_paths = CF::include_paths();
            $this->session_path = $include_paths[0] .'sessions' .DS;
            $this->default_session_path = $include_paths[0] .'sessions' .DS;
            $this->cookies_path = $include_paths[0] .'cookies' .DS;
            $this->default_cookies_path = $include_paths[0] .'cookies' .DS;
            $this->log_path = $include_paths[0] .'logs' .DS;
            $this->default_log_path = $include_paths[0] .'logs' .DS;
            foreach ($include_paths as $path) {
                if (is_dir($path)) {
                    $this->session_path = $path . 'sessions' . DS;
                    $this->default_session_path = $path . 'sessions' . DS;
                    $this->cookies_path = $path . 'cookies' . DS;
                    $this->default_cookies_path = $path . 'cookies' . DS;
                    $this->log_path = $path . 'logs' . DS;
                    $this->default_log_path = $path . 'logs' . DS;
                    if (!is_dir($this->default_session_path)) {
                        @mkdir($this->default_session_path);
                    }
                    if (!is_dir($this->default_cookies_path)) {
                        @mkdir($this->default_cookies_path);
                    }
                    if (!is_dir($this->default_log_path)) {
                        @mkdir($this->default_log_path);
                    }
                    break;
                }
            }
            if ($this->encrypt == true) {
                $this->session_id_encrypt = $session_id;
                $session_id = $this->decrypt($this->session_id_encrypt);
            }
            $this->session_id = $session_id;
            $this->have_cookies = $have_cookies;
            $this->data = array();

            if ($session_id == null || strlen($session_id) == 0) {
                $this->init();
            }
            else {
                $this->load();
            }
        }

        public static function instance($session_id = null, $have_cookies = false) {
            if (self::$instance == null) {
                self::$instance = new ApiSession($session_id, $have_cookies);
            }
            return self::$instance;
        }

        public function save($data = null) {
            if ($data != null) {
                $this->data = $data;
            }
            $filename = $this->session_path . $this->session_id . EXT;
            if ($this->using_fopen == true) {
                $data_val = cphp::save_value($this->data, null);
                $this->save_file($filename, $data_val);
            }
            else {
                cphp::save_value($this->data, $filename);
            }
            return $this;
        }

        public function save_file($filepath, $message) {
            if (!$fp = @fopen($filepath, 'w+')) {
                return FALSE;
            }

            flock($fp, LOCK_EX);
            fwrite($fp, $message);
            flock($fp, LOCK_UN);
            fclose($fp);

            //@chmod($filepath, FILE_WRITE_MODE);
        }

        public function load() {
            $this->callback_load();
            $filename = $this->session_path . $this->session_id . EXT;
            if (!file_exists($filename)) {
                $this->init();
            }
            

            if (!file_exists($filename)) {
                throw new Exception('Session file doesnt exists');
            }
            $this->data = cphp::load_value($filename);
            return $this;
        }

        public function set($key, $val) {
            if (!isset($this->data[$key]) || $this->data[$key] != $val) {
                $this->load();
                $this->data[$key] = $val;
                $this->save();
            }
            return $this;
        }

        public function get($key, $default = null) {
            if (isset($this->data[$key])) {
                return $this->data[$key];
            }
            return $default;
        }
        
        public function get_data() {
            return $this->data;
        }
        
        public function set_data($data) {
            $this->data = $data;
            $this->save();
            return $this;
        }
        
        public function init() {
            $prefix = date("YmdHis");
            $this->session_id = uniqid($prefix);

            $this->callback_init();
            $this->make_dir($this->session_path);

            
            if ($this->have_cookies == true) {
                $this->data['cookies_file'] = $this->cookies_path . $this->session_id;
            }

            $this->data['session_id'] = $this->session_id;
            $this->save(null);
            return $this;
        }

        public function callback_init() {
            // override this method
        }

        public function callback_load() {
            // override this method
        }

        private function make_dir($path) {
            $tmp_path = explode(DS, $path);

            $directory = '';
            foreach ($tmp_path as $tmp_path_k => $tmp_path_v) {
                $directory .= $tmp_path_v . DS;
                if (!is_dir($directory) && !file_exists($directory)) {
                    @mkdir($directory);
                }
            }
        }

//        public function get_session_id() {
//            return $this->session_id;
//        }
        
        public function get_session_id($encrypt = true) {
            if ($this->encrypt == false) {
                return $this->session_id;
            }
            else {
                if ($encrypt == true) {
                    return $this->encrypt($this->session_id);
                }
                else {
                    return $this->session_id;
                }
            }
        }

        public function error() {
            return ApiError::instance();
        }

        public function set_validate_expired($validate_expired) {
            $this->validate_expired = $validate_expired;
            return $this;
        }

        public function get_validate_expired() {
            return $this->validate_expired;
        }

        public function set_last_activity($last_activity) {
            $this->last_activity = $last_activity;
            return $this;
        }

        public function is_expired() {
            
        }
        
        public function get_session_file($include = false){
            $filename = $this->session_path . $this->session_id . EXT;
//            echo $filename;
            if (!file_exists($filename)) {
                throw new Exception('Session file doesnt exists');
            }
            if ($include == true) {
                $file = include $filename;
            }
            else {
                $file = file_get_contents($filename);
            }
            return $file;
        }

        public function get_session_path(){
            return $this->session_path;
        }

        public function get_log_path(){
            return $this->log_path;
        }
        
        public function encrypt($session_id) {
            $session_key = ccfg::get('session_key');
            if (strlen($session_key) == 0) {
                $session_key = 'ITTRONSESSION';
            }
            $str = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789");
            shuffle($str);
            $salt = '';
            foreach (array_rand($str, self::_digit) as $key) {
                $salt .= $str[$key];
            }

            $count_session_key = count($session_key);
            $chiper_text = '';
            for ($i = 0; $i < strlen($session_id); $i++) {
                $chiper_text .= $session_id[$i] ^ $session_key[$i % $count_session_key];
            }

            $chiper_text = base64_encode($chiper_text);
            $chiper_text = str_replace("/", "_", $chiper_text);
            return $chiper_text;
        }

        public function decrypt($chiper_text) {
            $session_key = ccfg::get('session_key');
            if (strlen($session_key) == 0) {
                $session_key = 'ITTRONSESSION';
            }
            $chiper_text = str_replace('_', '/', $chiper_text);
            $chiper_text = base64_decode($chiper_text);
            $count_session_key = count($session_key);

            $decrypt_text = '';
            for ($i = 0; $i < strlen($chiper_text); $i++) {
                $decrypt_text .= $chiper_text[$i] ^ $session_key[$i % $count_session_key];
            }
            return $decrypt_text;
        }

    }
    
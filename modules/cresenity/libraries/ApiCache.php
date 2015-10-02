<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Oct 2, 2015
     * @license http://piposystem.com Piposystem
     */
    class ApiCache {

        public static $instance = null;
        protected $full_path = null;
        protected $is_exists = false;
        protected $cache_data;
        protected $expired;

        protected function __construct($full_path = '') {
            $this->is_exists = false;
            $this->cache_data = null;
            $this->full_path = $full_path;
        }

        public static function instance($full_path = '') {
            if (self::$instance == null) {
                self::$instance = new ApiCache($full_path);
            }
            return self::$instance;
        }

        public function get_content() {
            $this->build_path();
            $is_exists = $this->is_exists();
            if ($is_exists === true) {
                return include $this->full_path;
            }
            return $this->is_exists;
        }

        public function save($cache_data) {
            $this->build_path();
            $this->cache_data = $cache_data;
            cphp::save_value($this->cache_data, $this->full_path);
            return $this;
        }

        public function is_expired() {
            $file = $this->full_path;
            $date_modified = filemtime($file);
            $difference = (time() - $date_modified) / 60;
            if (floor($difference) >= $this->expired) {
                return TRUE;
            }
            return FALSE;
        }

        // override this method
        protected function build_path() {
            
        }

        public function set_path($path) {
            $this->path = $path;
            return $this;
        }

        public function is_exists() {
            $this->build_path();
            if (!file_exists($this->full_path)) {
                $this->is_exists = false;
            }
            else {
                if ($this->is_expired()) {
                    $this->is_exists = false;
                }
                else {
                    $this->is_exists = true;
                }
            }
            return $this->is_exists;
        }

        public function set_full_path($full_path) {
            $this->full_path = $full_path;
            return $this;
        }

        public function set_expired($expired) {
            $this->expired = $expired;
            return $this;
        }

    }
    
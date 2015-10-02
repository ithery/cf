<?php

    defined('SYSPATH') OR die('No direct access allowed.');
    require_once dirname(__FILE__) . "/Lib/csysinfo/CSysInfo.php";

    class CCache extends CObject {

        protected static $_instance;

        public function __construct($id = "") {
            parent::__construct($id);
        }

        public static function instance($id = "") {
            if (self::$_instance == null) self::$_instance = new CCache($id);
            return self::$_instance;
        }

        public function get($id) {
            return Cache::instance()->get($id);
        }

        public function find($tag) {
            return Cache::instance()->find($tag);
        }

        public function set($id, $data, $tags = NULL, $lifetime = NULL) {
            return Cache::instance()->set($id, $data, $tags, $lifetime);
        }

        public function delete($id) {
            return Cache::instance()->delete($id);
        }

        public function delete_tag($tag) {
            return Cache::instance()->delete_tag($tag);
        }

        public function delete_all($tag) {
            return Cache::instance()->delete_all($tag);
        }

    }
    
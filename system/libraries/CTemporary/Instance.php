<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CTemporary_Instance {

    protected static $instance;

    public static function instance($disk = null) {
        if ($disk == null) {
            $disk = CF::config('storage.temp');
        }

        if (static::$instance == null) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$disk])) {
            static::$instance[$disk] = new CTemporary_Instance($disk);
        }
        return static::$instance[$disk];
    }

    public function __construct($disk) {
        $this->disk = $disk;
    }

    protected function disk() {
        return CStorage::instance($this->disk);
    }

    
    public function __call($name, $arguments) {
        return $this->disk()->$name(...$arguments);
    }
}

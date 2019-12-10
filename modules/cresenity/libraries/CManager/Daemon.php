<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CManager_Daemon {

    protected static $instance;
    protected $daemons = array();

    public static function instance() {

        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function registerDaemon($class) {
        $this->daemons[] = $class;
    }

    public function daemons() {
        return $this->daemons;
    }

}

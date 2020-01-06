<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Output {

    private static $instance;
    protected $messages;

    public function instance() {
        if (static::$instance == null) {
            static::$instance = new CBackup_Output();
        }
        return static::$instance;
    }

    private function __construct() {
        $this->clear();
    }

    public function info($info) {
        $this->messages[] = $info;
    }

    public function clear() {
        $this->messages = [];
    }
}

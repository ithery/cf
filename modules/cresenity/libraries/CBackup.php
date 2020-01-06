<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup {

    public static function createJob($options) {
        return CBackup_Factory::createJob($config);
    }

    public static function output() {
        return CBackup_Output::instance();
    }

}

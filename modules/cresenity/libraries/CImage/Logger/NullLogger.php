<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Psr\Log\LoggerInterface;

class CImage_Logger_NullLogger implements LoggerInterface {

    public function emergency($message, array $context = []) {
        
    }

    public function alert($message, array $context = []) {
        
    }

    public function critical($message, array $context = []) {
        
    }

    public function error($message, array $context = []) {
        
    }

    public function warning($message, array $context = []) {
        
    }

    public function notice($message, array $context = []) {
        
    }

    public function info($message, array $context = []) {
        
    }

    public function debug($message, array $context = []) {
        
    }

    public function log($level, $message, array $context = []) {
        
    }

}

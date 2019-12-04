<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CFApp {
    public function __construct() {
        
        spl_autoload_register(array(__CLASS__, 'autoLoading'));
    }
    
    
    
    public static function autoLoading() {
        
    }
}
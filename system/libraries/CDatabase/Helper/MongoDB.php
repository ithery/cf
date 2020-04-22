<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDatabase_Helper_MongoDB {
    
    public static function commandToString($commands) {
        
        return 'db.runCommand('.json_encode($commands).')';
        
    }
    
    public static function stringToCommand() {
        
    }
}
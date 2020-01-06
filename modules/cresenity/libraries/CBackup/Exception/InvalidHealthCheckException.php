<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Exception_InvalidHealthCheckException extends Exception {

    public static function because($message) {
        return new static($message);
    }

}

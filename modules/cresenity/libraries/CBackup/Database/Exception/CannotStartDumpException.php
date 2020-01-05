<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Database_Exception_CannotStartDumpException extends Exception {

    /**
     * @param string $name
     *
     * @return CDatabase_Database_Exception_CannotStartDumpException
     */
    public static function emptyParameter($name) {
        return new static("Parameter `{$name}` cannot be empty.");
    }

}

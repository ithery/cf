<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CBackup_Database_Exception_CannotSetParameterException extends Exception {

    /**
     * @param string $name
     * @param string $conflictName
     *
     * @return CDatabase_Database_Exception_CannotSetParameterException
     */
    public static function conflictingParameters($name, $conflictName) {
        return new static("Cannot set `{$name}` because it conflicts with parameter `{$conflictName}`.");
    }

}

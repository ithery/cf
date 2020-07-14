<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Exception_UnreadableFileException extends Exception implements CExporter_ExceptionInterface {

    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'File could not be read', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

<?php

class CExporter_Exception_UnreadableFileException extends Exception implements CExporter_ExceptionInterface {
    /**
     * @param string         $message
     * @param int            $code
     * @param null|Throwable $previous
     */
    public function __construct($message = 'File could not be read', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

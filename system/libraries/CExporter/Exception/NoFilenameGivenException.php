<?php

class CExporter_Exception_NoFilenameGivenException extends InvalidArgumentException implements CExporter_ExceptionInterface {
    /**
     * @param string         $message
     * @param int            $code
     * @param null|Throwable $previous
     */
    public function __construct($message = 'A filename needs to be passed in order to download the export', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}

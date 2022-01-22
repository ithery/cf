<?php

class CExporter_Exception_NoFilePathGivenException extends InvalidArgumentException implements CExporter_ExceptionInterface {
    /**
     * @param string         $message
     * @param int            $code
     * @param null|Throwable $previous
     */
    public function __construct($message = 'A filepath needs to be passed.', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return CExporter_Exception_NoFilePathGivenException
     */
    public static function import() {
        return new static('A filepath or UploadedFile needs to be passed to start the import.');
    }

    /**
     * @return CExporter_Exception_NoFilePathGivenException
     */
    public static function export() {
        return new static('A filepath needs to be passed in order to store the export.');
    }
}

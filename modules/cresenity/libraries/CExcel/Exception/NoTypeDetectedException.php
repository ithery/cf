<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 1, 2019, 4:02:35 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CExcel_Exception_NoTypeDetectedException extends CExcel_Exception implements CExcel_ExceptionInterface {

    /**
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($message = 'No ReaderType or WriterType could be detected. Make sure you either pass a valid extension to the filename or pass an explicit type.', $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

}

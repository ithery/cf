<?php

class CExporter_Exception_ConcernConflictException extends LogicException implements CExporter_ExceptionInterface {
    /**
     * @return CExporter_Exception_ConcernConflictException
     */
    public static function queryOrCollectionAndView() {
        return new static('Cannot use FromQuery, FromArray or FromCollection and FromView on the same sheet.');
    }
}

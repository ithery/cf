<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Exception_ConcernConflictException extends LogicException implements CExporter_ExceptionInterface {

    /**
     * @return ConcernConflictException
     */
    public static function queryOrCollectionAndView() {
        return new static('Cannot use FromQuery, FromArray or FromCollection and FromView on the same sheet.');
    }

}

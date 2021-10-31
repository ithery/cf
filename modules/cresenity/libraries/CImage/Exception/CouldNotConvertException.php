<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CImage_Exception_CouldNotConvertException extends Exception {

    public static function unknownManipulation($operationName) {
        return new self("Can not convert image. Unknown operation `{$operationName}` used");
    }

}

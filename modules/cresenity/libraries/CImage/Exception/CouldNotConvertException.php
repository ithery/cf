<?php

class CImage_Exception_CouldNotConvertException extends Exception {
    public static function unknownManipulation($operationName) {
        return new self("Can not convert image. Unknown operation `{$operationName}` used");
    }
}

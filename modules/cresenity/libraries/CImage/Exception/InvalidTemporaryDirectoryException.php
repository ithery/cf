<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CImage_Exception_InvalidTemporaryDirectoryException extends Exception {

    public static function temporaryDirectoryNotCreatable($directory) {
        return new self("the temporary directory `{$directory}` does not exist and can not be created");
    }

    public static function temporaryDirectoryNotWritable($directory) {
        return new self("the temporary directory `{$directory}` does exist but is not writable");
    }

}

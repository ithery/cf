<?php

class CImage_Exception_InvalidTemporaryDirectoryException extends Exception {
    public static function temporaryDirectoryNotCreatable($directory) {
        return new self("the temporary directory `{$directory}` does not exist and can not be created");
    }

    public static function temporaryDirectoryNotWritable($directory) {
        return new self("the temporary directory `{$directory}` does exist but is not writable");
    }
}

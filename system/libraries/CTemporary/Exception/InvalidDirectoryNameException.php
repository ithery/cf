<?php

class CTemporary_Exception_InvalidDirectoryNameException extends \Exception {
    /**
     * @param string $directoryName
     *
     * @return static
     */
    public static function create($directoryName) {
        return new static("The directory name `{$directoryName}` contains invalid characters.");
    }
}

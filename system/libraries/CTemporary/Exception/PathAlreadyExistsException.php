<?php

class CTemporary_Exception_PathAlreadyExistsException extends \Exception {
    /**
     * @param string $path
     *
     * @return static
     */
    public static function create($path) {
        return new static("Path `{$path}` already exists.");
    }
}

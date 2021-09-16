<?php

class CResources_Exception_InvalidUrlGenerator extends CResources_Exception {
    public static function doesntExist($class) {
        return new static("Class {$class} doesn't exist");
    }

    public static function isntAUrlGenerator($class) {
        return new static("Class {$class} must implement `Spatie\\MediaLibrary\\UrlGenerator\\UrlGenerator`");
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

class CResources_Exception_InvalidPathGenerator extends CResources_Exception {
    public static function doesntExist($class) {
        return new static("Class {$class} doesn't exist");
    }

    public static function isntAPathGenerator($class) {
        return new static("Class {$class} must implement `CResources_PathGeneratorInterface`");
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public static function doesNotImplementPathGenerator($class) {
        $pathGeneratorClass = CResources_PathGeneratorInterface::class;

        return new static("Path generator class `{$class}` must implement `${pathGeneratorClass}}`");
    }
}

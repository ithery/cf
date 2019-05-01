<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 1:24:32 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_Factory {

    public static function createPathGenerator() {
        $pathGeneratorClass = CResources_PathGenerator::class;
        $customPathClass = CF::config('resource.path_generator');
        if ($customPathClass) {
            $pathGeneratorClass = $customPathClass;
        }
        static::guardAgainstInvalidPathGenerator($pathGeneratorClass);
        return new $pathGeneratorClass();
    }

    protected static function guardAgainstInvalidPathGenerator($pathGeneratorClass) {
        if (!class_exists($pathGeneratorClass)) {
            throw CResources_Exception_InvalidPathGenerator::doesntExist($pathGeneratorClass);
        }
        if (!is_subclass_of($pathGeneratorClass, CResources_PathGeneratorInterface::class)) {
            throw CResources_Exception_InvalidPathGenerator::isntAPathGenerator($pathGeneratorClass);
        }
    }

}

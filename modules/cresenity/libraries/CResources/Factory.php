<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 2, 2019, 1:24:32 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_Factory {

    /**
     * 
     * @return CResources_PathGenerator
     */
    public static function createPathGenerator() {
        $pathGeneratorClass = CResources_PathGenerator::class;
        $customPathClass = CF::config('resource.path_generator');
        if ($customPathClass) {
            $pathGeneratorClass = $customPathClass;
        }
        static::guardAgainstInvalidPathGenerator($pathGeneratorClass);
        return new $pathGeneratorClass();
    }

    /**
     * 
     * @return \CResources_FileManipulator
     */
    public static function createFileManipulator() {
        return new CResources_FileManipulator();
    }

    /**
     * 
     * @param string $pathGeneratorClass
     * @throws CResources_Exception_InvalidPathGenerator
     */
    protected static function guardAgainstInvalidPathGenerator($pathGeneratorClass) {
        if (!class_exists($pathGeneratorClass)) {
            throw CResources_Exception_InvalidPathGenerator::doesntExist($pathGeneratorClass);
        }
        if (!is_subclass_of($pathGeneratorClass, CResources_PathGeneratorInterface::class)) {
            throw CResources_Exception_InvalidPathGenerator::isntAPathGenerator($pathGeneratorClass);
        }
    }

}

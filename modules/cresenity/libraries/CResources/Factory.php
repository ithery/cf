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
     * @return CResources_PathGenerator
     */
    public static function createUrlGeneratorForResource(CApp_Model_Interface_ResourceInterface $resource, $conversionName = '') {
        $urlGeneratorClass = CF::config('resource.url_generator') != null ?
                CF::config('resource.url_generator') : 'CResources_UrlGenerator_' . ucfirst($resource->getDiskDriverName()) . 'UrlGenerator';
       
        static::guardAgainstInvalidUrlGenerator($urlGeneratorClass);
        $urlGenerator = new $urlGeneratorClass();
        $pathGenerator = static::createPathGenerator();
        $urlGenerator
                ->setResource($resource)
                ->setPathGenerator($pathGenerator);
        if ($conversionName !== '') {
            $conversion = CResources_ConversionCollection::createForResource($resource)->getByName($conversionName);
            $urlGenerator->setConversion($conversion);
        }
        return $urlGenerator;
    }

    public static function guardAgainstInvalidUrlGenerator($urlGeneratorClass) {
        if (!class_exists($urlGeneratorClass)) {
            throw CResources_Exception_InvalidUrlGenerator::doesntExist($urlGeneratorClass);
        }
        if (!is_subclass_of($urlGeneratorClass, CResources_UrlGeneratorInterface::class)) {
            throw CResources_Exception_InvalidUrlGenerator::isntAUrlGenerator($urlGeneratorClass);
        }
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
     * @return \CResources_FileSystem
     */
    public static function createFileSystem() {
        return new CResources_Filesystem();
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

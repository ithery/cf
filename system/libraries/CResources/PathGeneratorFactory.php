<?php

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Exceptions\InvalidPathGenerator;

class CResources_PathGeneratorFactory {
    /**
     * @param CModel_Resource_ResourceInterface $resource
     *
     * @return CResources_PathGeneratorInterface
     */
    public static function create(CModel_Resource_ResourceInterface $resource) {
        $pathGeneratorClass = self::getPathGeneratorClass($resource);

        static::guardAgainstInvalidPathGenerator($pathGeneratorClass);

        return app($pathGeneratorClass);
    }

    protected static function getPathGeneratorClass(CModel_Resource_ResourceInterface $resource) {
        $defaultPathGeneratorClass = CF::config('resource.path_generator');

        foreach (CF::config('resource.custom_path_generators', []) as $modelClass => $customPathGeneratorClass) {
            if (is_a($resource->model_type, $modelClass, true)) {
                return $customPathGeneratorClass;
            }
        }

        return $defaultPathGeneratorClass;
    }

    protected static function guardAgainstInvalidPathGenerator(string $pathGeneratorClass): void {
        if (!class_exists($pathGeneratorClass)) {
            throw InvalidPathGenerator::doesntExist($pathGeneratorClass);
        }

        if (!is_subclass_of($pathGeneratorClass, PathGenerator::class)) {
            throw InvalidPathGenerator::doesNotImplementPathGenerator($pathGeneratorClass);
        }
    }
}

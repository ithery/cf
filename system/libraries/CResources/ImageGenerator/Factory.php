<?php

class CResources_ImageGenerator_Factory {
    /**
     * @return CCollection
     */
    public static function getImageGenerators() {
        return c::collect(CF::config('resource.image_generators'))
            ->map(function ($imageGeneratorClassName, $key) {
                $imageGeneratorConfig = [];

                if (!is_numeric($key)) {
                    $imageGeneratorConfig = $imageGeneratorClassName;
                    $imageGeneratorClassName = $key;
                }

                return c::container($imageGeneratorClassName, $imageGeneratorConfig);
            });
    }

    /**
     * Undocumented function.
     *
     * @param null|string $extension
     *
     * @return null|CResources_ImageGenerator_FileTypeAbstract
     */
    public static function forExtension($extension = null) {
        return static::getImageGenerators()
            ->first(function (CResources_ImageGenerator_FileTypeAbstract $imageGenerator) use ($extension) {
                return $imageGenerator->canHandleExtension(strtolower($extension));
            });
    }

    /**
     * @param null|string $mimeType
     *
     * @return null|CResources_ImageGenerator_FileTypeAbstract
     */
    public static function forMimeType($mimeType = null) {
        if (is_null($mimeType)) {
            return null;
        }

        return static::getImageGenerators()
            ->first(function (CResources_ImageGenerator_FileTypeAbstract $imageGenerator) use ($mimeType) {
                return $imageGenerator->canHandleMime($mimeType);
            });
    }

    /**
     * @param CModel_Resource_ResourceInterface $resource
     *
     * @return null|CResources_ImageGenerator_FileTypeAbstract
     */
    public static function forResource(CModel_Resource_ResourceInterface $resource) {
        return static::getImageGenerators()
            ->first(function (CResources_ImageGenerator_FileTypeAbstract $imageGenerator) use ($resource) {
                return $imageGenerator->canConvert($resource);
            });
    }
}

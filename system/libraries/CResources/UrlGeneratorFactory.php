<?php

class CResources_UrlGeneratorFactory {
    /**
     * @param CModel_Resource_ResourceInterface $resource
     * @param string                            $conversionName
     *
     * @return CResources_UrlGeneratorInterface
     */
    public static function createForResource(CModel_Resource_ResourceInterface $resource, $conversionName = '') {
        $urlGeneratorClass = CF::config('resoure.url_generator');

        if ($urlGeneratorClass == null) {
            $diskDriverName = $resource->getDiskDriverName();
            $urlGeneratorClass = CResources_UrlGenerator_DefaultUrlGenerator::class;
            if (strlen($diskDriverName) > 0) {
                $urlGeneratorClass = 'CResources_UrlGenerator_' . ucfirst($diskDriverName) . 'UrlGenerator';
            }
        }

        static::guardAgainstInvalidUrlGenerator($urlGeneratorClass);

        /** @var \CResources_UrlGeneratorInterface $urlGenerator */
        $urlGenerator = c::container($urlGeneratorClass);

        $pathGenerator = CResources_PathGeneratorFactory::create($resource);

        $urlGenerator
            ->setResource($resource)
            ->setPathGenerator($pathGenerator);

        if ($conversionName !== '') {
            $conversion = CResources_ConversionCollection::createForResource($resource)->getByName($conversionName);

            $urlGenerator->setConversion($conversion);
        }

        return $urlGenerator;
    }

    /**
     * @param string $urlGeneratorClass
     *
     * @return void
     */
    public static function guardAgainstInvalidUrlGenerator($urlGeneratorClass) {
        if (!class_exists($urlGeneratorClass)) {
            throw CResources_Exception_InvalidUrlGenerator::doesntExist($urlGeneratorClass);
        }

        if (!is_subclass_of($urlGeneratorClass, CResources_UrlGeneratorInterface::class)) {
            throw CResources_Exception_InvalidUrlGenerator::doesNotImplementUrlGenerator($urlGeneratorClass);
        }
    }
}

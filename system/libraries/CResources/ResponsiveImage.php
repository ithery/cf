<?php
class CResources_ResponsiveImage {
    /**
     * @var CModel_Resource_ResourceInterface|CModel
     */
    public $resource;

    /**
     * @var string
     */
    public $fileName;

    public static function register(CModel_Resource_ResourceInterface $resource, $fileName, $conversionName) {
        $responsiveImages = $resource->responsive_images;

        $responsiveImages[$conversionName]['urls'][] = $fileName;

        $resource->responsive_images = $responsiveImages;
        /** @var CModel_Resource_ResourceInterface|CModel $resource */
        $resource->save();
    }

    public static function registerTinySvg(CModel_Resource_ResourceInterface $resource, string $base64Svg, string $conversionName) {
        $responsiveImages = $resource->responsive_images;

        $responsiveImages[$conversionName]['base64svg'] = $base64Svg;

        $resource->responsive_images = $responsiveImages;

        /** @var CModel_Resource_ResourceInterface|CModel $resource */
        $resource->save();
    }

    public function __construct($fileName, $resource) {
        $this->fileName = $fileName;
        $this->resource = $resource;
    }

    public function url(): string {
        $conversionName = '';

        if ($this->generatedFor() !== 'resource_original') {
            $conversionName = $this->generatedFor();
        }

        $urlGenerator = CResources_UrlGeneratorFactory::createForResource($this->resource, $conversionName);

        return $urlGenerator->getResponsiveImagesDirectoryUrl() . rawurlencode($this->fileName);
    }

    public function generatedFor(): string {
        $propertyParts = $this->getPropertyParts();

        array_pop($propertyParts);

        array_pop($propertyParts);

        return implode('_', $propertyParts);
    }

    public function width(): int {
        $propertyParts = $this->getPropertyParts();

        array_pop($propertyParts);

        return (int) c::last($propertyParts);
    }

    public function height(): int {
        $propertyParts = $this->getPropertyParts();

        return (int) c::last($propertyParts);
    }

    protected function getPropertyParts(): array {
        $propertyString = $this->stringBetween($this->fileName, '___', '.');

        return explode('_', $propertyString);
    }

    protected function stringBetween(string $subject, string $startCharacter, string $endCharacter): string {
        $lastPos = strrpos($subject, $startCharacter);

        $between = substr($subject, $lastPos);

        $between = str_replace('___', '', $between);

        $between = strstr($between, $endCharacter, true);

        return $between;
    }

    public function delete(): self {
        $pathGenerator = CResources_PathGeneratorFactory::create($this->resource);

        $path = $pathGenerator->getPathForResponsiveImages($this->resource);

        $fullPath = $path . $this->fileName;

        CResources_Factory::createFileSystem()->removeFile($this->resource, $fullPath);

        $responsiveImages = $this->resource->responsive_images;

        unset($responsiveImages[$this->generatedFor()]);

        $this->resource->responsive_images = $responsiveImages;

        $this->resource->save();

        return $this;
    }
}

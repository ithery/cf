<?php
class CResource_ResponsiveImage_RegisteredResponsiveImage {
    /**
     * @var CCollection
     */
    public $files;

    /**
     * @var string
     */
    public $generatedFor;

    /**
     * @var CModel_Resource_ResourceInterface
     */
    protected $resource;

    public function __construct(CModel_Resource_ResourceInterface $resource, $conversionName = '') {
        $this->resource = $resource;
        $this->generatedFor = $conversionName === ''
            ? 'resource_original'
            : $conversionName;

        $this->files = c::collect($resource->responsive_images[$this->generatedFor]['urls'] ?? [])
            ->map(function ($fileName) use ($resource) {
                return new CResources_ResponsiveImage($fileName, $resource);
            })
            ->filter(function (CResources_ResponsiveImage $responsiveImage) {
                return $responsiveImage->generatedFor() === $this->generatedFor;
            });
    }

    public function getUrls(): array {
        return $this->files
            ->map(function (CResources_ResponsiveImage $responsiveImage) {
                return $responsiveImage->url();
            })->values()
            ->toArray();
    }

    public function getSrcset(): string {
        $filesSrcset = $this->files
            ->map(function (CResources_ResponsiveImage $responsiveImage) {
                return "{$responsiveImage->url()} {$responsiveImage->width()}w";
            })->implode(', ');

        $shouldAddPlaceholderSvg = CF::config('resource.responsive_images.use_tiny_placeholders')
            && $this->getPlaceholderSvg();

        if ($shouldAddPlaceholderSvg) {
            $filesSrcset .= ', ' . $this->getPlaceholderSvg() . ' 32w';
        }

        return $filesSrcset;
    }

    /**
     * @return null|string
     */
    public function getPlaceholderSvg() {
        return $this->media->responsive_images[$this->generatedFor]['base64svg'] ?? null;
    }

    public function delete() {
        $this->files->each->delete();

        $responsiveImages = $this->media->responsive_images;

        unset($responsiveImages[$this->generatedFor]);

        $this->media->responsive_images = $responsiveImages;

        $this->media->save();
    }
}

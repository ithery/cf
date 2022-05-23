<?php

class CResources_HtmlableMedia implements CInterface_Htmlable, \Stringable {
    /**
     * @var string
     */
    protected $conversionName = '';

    /**
     * @var array
     */
    protected $extraAttributes = [];

    /**
     * @var CModel_Resource_ResourceInterface
     */
    protected $resource;

    /**
     * @var string
     */
    protected $loadingAttributeValue = '';

    public function __construct($resource) {
        $this->resource = $resource;
    }

    /**
     * @param array $attributes
     *
     * @return self
     */
    public function attributes($attributes) {
        $this->extraAttributes = $attributes;

        return $this;
    }

    /**
     * @param string $conversionName
     *
     * @return self
     */
    public function conversion($conversionName) {
        $this->conversionName = $conversionName;

        return $this;
    }

    /**
     * @return self
     */
    public function lazy() {
        $this->loadingAttributeValue = ('lazy');

        return $this;
    }

    public function toHtml() {
        $imageGenerator = CResources_ImageGenerator_Factory::forResource($this->resource) ?: new CResources_ImageGenerator_FileType_ImageType();

        if (!$imageGenerator->canHandleMime($this->resource->mime_type)) {
            return '';
        }

        $attributeString = c::collect($this->extraAttributes)
            ->map(function ($value, $name) {
                return $name . '="' . $value . '"';
            })->implode(' ');

        if (strlen($attributeString)) {
            $attributeString = ' ' . $attributeString;
        }

        $loadingAttributeValue = CF::config('resource.default_loading_attribute_value');

        if ($this->conversionName !== '') {
            $conversionObject = CResources_ConversionCollection::createForResource($this->resource)->getByName($this->conversionName);

            $loadingAttributeValue = $conversionObject->getLoadingAttributeValue();
        }

        if ($this->loadingAttributeValue !== '') {
            $loadingAttributeValue = $this->loadingAttributeValue;
        }

        $viewName = 'image';
        $width = '';
        $height = '';

        if ($this->resource->hasResponsiveImages($this->conversionName)) {
            $viewName = CF::config('resource.responsive_images.use_tiny_placeholders')
                ? 'responsive-image-with-placeholder'
                : 'responsive-image';

            $responsiveImage = $this->resource->responsiveImages($this->conversionName)->files->first();

            $width = $responsiveImage->width();
            $height = $responsiveImage->height();
        }

        $media = $this->resource;
        $conversion = $this->conversionName;

        return c::view("cresenity.resource.{$viewName}", compact(
            'media',
            'conversion',
            'attributeString',
            'loadingAttributeValue',
            'width',
            'height',
        ))->render();
    }

    public function __toString() {
        return $this->toHtml();
    }
}

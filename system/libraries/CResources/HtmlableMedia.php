<?php

use CInterface_Htmlable;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Conversions\ConversionCollection;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Image;
use Spatie\MediaLibrary\Conversions\ImageGenerators\ImageGeneratorFactory;

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
        $imageGenerator = CResources_Factory::ImageGeneratorFactory::forMedia($this->media) ?? new Image();

        if (!$imageGenerator->canHandleMime($this->media->mime_type)) {
            return '';
        }

        $attributeString = collect($this->extraAttributes)
            ->map(fn ($value, $name) => $name . '="' . $value . '"')->implode(' ');

        if (strlen($attributeString)) {
            $attributeString = ' ' . $attributeString;
        }

        $loadingAttributeValue = config('media-library.default_loading_attribute_value');

        if ($this->conversionName !== '') {
            $conversionObject = ConversionCollection::createForMedia($this->media)->getByName($this->conversionName);

            $loadingAttributeValue = $conversionObject->getLoadingAttributeValue();
        }

        if ($this->loadingAttributeValue !== '') {
            $loadingAttributeValue = $this->loadingAttributeValue;
        }

        $viewName = 'image';
        $width = '';
        $height = '';

        if ($this->media->hasResponsiveImages($this->conversionName)) {
            $viewName = config('media-library.responsive_images.use_tiny_placeholders')
                ? 'responsiveImageWithPlaceholder'
                : 'responsiveImage';

            $responsiveImage = $this->media->responsiveImages($this->conversionName)->files->first();

            $width = $responsiveImage->width();
            $height = $responsiveImage->height();
        }

        $media = $this->media;
        $conversion = $this->conversionName;

        return view("media-library::{$viewName}", compact(
            'media',
            'conversion',
            'attributeString',
            'loadingAttributeValue',
            'width',
            'height',
        ))->render();
    }

    public function __toString(): string {
        return $this->toHtml();
    }
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 1, 2019, 11:37:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Model_Resource extends CApp_Model implements CApp_Model_Interface_ResourceInterface {

    use CApp_Model_Trait_Resource;

    const TYPE_OTHER = 'other';

    protected $table = 'resource';
    protected $guarded = ['resource_id'];
    protected $casts = [
        'manipulations' => 'array',
        'custom_properties' => 'array',
        'responsive_images' => 'array',
    ];

    public function model() {
        return $this->morphTo();
    }

    /*
     * Get the full url to a original media file.
     */

    public function getFullUrl($conversionName = '') {
        return url($this->getUrl($conversionName));
    }

    /*
     * Get the url to a original media file.
     */

    public function getUrl($conversionName = '') {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);
        return $urlGenerator->getUrl();
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, $conversionName = '', array $options = []) {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);
        return $urlGenerator->getTemporaryUrl($expiration, $options);
    }

    /*
     * Get the path to the original media file.
     */

    public function getPath($conversionName = '') {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this, $conversionName);
        return $urlGenerator->getPath();
    }

    public function getImageGenerators() {
        $imageGenerators = CConfig::instance('resource', 'image_generators');

        return collect(config('medialibrary.image_generators'));
    }

    public function getTypeAttribute() {
        $type = $this->getTypeFromExtension();
        if ($type !== self::TYPE_OTHER) {
            return $type;
        }
        return $this->getTypeFromMime();
    }

    public function getTypeFromExtension() {
        $imageGenerator = $this->getImageGenerators()
                        ->map(function ( $className) {
                            return app($className);
                        })
                ->first->canHandleExtension(strtolower($this->extension));
        return $imageGenerator ? $imageGenerator->getType() : static::TYPE_OTHER;
    }

    public function getTypeFromMime() {
        $imageGenerator = $this->getImageGenerators()
                        ->map(function ( $className) {
                            return app($className);
                        })
                ->first->canHandleMime($this->mime_type);
        return $imageGenerator ? $imageGenerator->getType() : static::TYPE_OTHER;
    }

    public function getExtensionAttribute() {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getHumanReadableSizeAttribute() {
        return File::getHumanReadableSize($this->size);
    }

    public function getDiskDriverName() {
        return strtolower(config("filesystems.disks.{$this->disk}.driver"));
    }

    /*
     * Determine if the media item has a custom property with the given name.
     */

    public function hasCustomProperty($propertyName) {
        return array_has($this->custom_properties, $propertyName);
    }

    /**
     * Get the value of custom property with the given name.
     *
     * @param string $propertyName
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCustomProperty($propertyName, $default = null) {
        return array_get($this->custom_properties, $propertyName, $default);
    }

    /**
     * @param string $name
     * @param mixed $value
     *
     * @return $this
     */
    public function setCustomProperty($name, $value) {
        $customProperties = $this->custom_properties;
        array_set($customProperties, $name, $value);
        $this->custom_properties = $customProperties;
        return $this;
    }

    public function forgetCustomProperty($name) {
        $customProperties = $this->custom_properties;
        array_forget($customProperties, $name);
        $this->custom_properties = $customProperties;
        return $this;
    }

    /*
     * Get all the names of the registered media conversions.
     */

    public function getMediaConversionNames() {
        $conversions = ConversionCollection::createForMedia($this);
        return $conversions->map(function (Conversion $conversion) {
                    return $conversion->getName();
                })->toArray();
    }

    public function hasGeneratedConversion($conversionName) {
        $generatedConversions = $this->getGeneratedConversions();
        return isset($generatedConversions[$conversionName]) ? $generatedConversions[$conversionName] : false;
    }

    public function markAsConversionGenerated($conversionName, $generated) {
        $this->setCustomProperty("generated_conversions.{$conversionName}", $generated);
        $this->save();
        return $this;
    }

    public function getGeneratedConversions() {
        return collect($this->getCustomProperty('generated_conversions', []));
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function toResponse($request) {
        $downloadHeaders = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Type' => $this->mime_type,
            'Content-Length' => $this->size,
            'Content-Disposition' => 'attachment; filename="' . $this->file_name . '"',
            'Pragma' => 'public',
        ];
        return response()->stream(function () {
                    $stream = $this->stream();
                    fpassthru($stream);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }, 200, $downloadHeaders);
    }

    public function getResponsiveImageUrls($conversionName = '') {
        return $this->responsiveImages($conversionName)->getUrls();
    }

    public function hasResponsiveImages($conversionName = '') {
        return count($this->getResponsiveImageUrls($conversionName)) > 0;
    }

    public function getSrcset($conversionName = '') {
        return $this->responsiveImages($conversionName)->getSrcset();
    }

    public function toHtml() {
        return $this->img();
    }

    /**
     * @param string|array $conversion
     * @param array $extraAttributes
     *
     * @return string
     */
    public function img($conversion = '', array $extraAttributes = []) {
        if (!(new Image())->canHandleMime($this->mime_type)) {
            return '';
        }
        if (is_array($conversion)) {
            $attributes = $conversion;
            $conversion = isset($attributes['conversion']) ? $attributes['conversion'] : '';
            unset($attributes['conversion']);
            $extraAttributes = array_merge($attributes, $extraAttributes);
        }
        $attributeString = collect($extraAttributes)
                        ->map(function ($value, $name) {
                            return $name . '="' . $value . '"';
                        })->implode(' ');
        if (strlen($attributeString)) {
            $attributeString = ' ' . $attributeString;
        }
        $media = $this;
        $viewName = 'image';
        $width = '';
        if ($this->hasResponsiveImages($conversion)) {
            $viewName = config('medialibrary.responsive_images.use_tiny_placeholders') ? 'responsiveImageWithPlaceholder' : 'responsiveImage';
            $width = $this->responsiveImages($conversion)->files->first()->width();
        }
        return view("medialibrary::{$viewName}", compact(
                        'media', 'conversion', 'attributeString', 'width'
        ));
    }

    public function move(HasMedia $model, $collectionName = 'default') {
        $newMedia = $this->copy($model, $collectionName);
        $this->delete();
        return $newMedia;
    }

    public function copy(HasMedia $model, $collectionName = 'default') {
        $temporaryDirectory = TemporaryDirectory::create();
        $temporaryFile = $temporaryDirectory->path($this->file_name);
        app(Filesystem::class)->copyFromMediaLibrary($this, $temporaryFile);
        $newMedia = $model
                ->addMedia($temporaryFile)
                ->usingName($this->name)
                ->withCustomProperties($this->custom_properties)
                ->toMediaCollection($collectionName);
        $temporaryDirectory->delete();
        return $newMedia;
    }

    public function responsiveImages($conversionName = '') {
        return new RegisteredResponsiveImages($this, $conversionName);
    }

    public function stream() {
        $filesystem = app(Filesystem::class);
        return $filesystem->getStream($this);
    }

    public function __invoke(...$arguments) {
        return new HtmlString($this->img(...$arguments));
    }

}

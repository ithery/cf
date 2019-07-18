<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 30, 2019, 3:18:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_HasSlug_SlugOptions {

    /** @var array|callable */
    public $generateSlugFrom;

    /** @var string */
    public $slugField;

    /** @var bool */
    public $generateUniqueSlugs = true;

    /** @var int */
    public $maximumLength = 250;

    /** @var bool */
    public $generateSlugsOnCreate = true;

    /** @var bool */
    public $generateSlugsOnUpdate = true;

    /** @var string */
    public $slugSeparator = '-';

    /** @var string */
    public $slugLanguage = 'en';

    public static function create() {
        return new static();
    }

    /**
     * @param string|array|callable $fieldName
     *
     * @return CModel_HasSlug_SlugOptions
     */
    public function generateSlugsFrom($fieldName) {
        if (is_string($fieldName)) {
            $fieldName = [$fieldName];
        }
        $this->generateSlugFrom = $fieldName;
        return $this;
    }

    public function saveSlugsTo($fieldName) {
        $this->slugField = $fieldName;
        return $this;
    }

    public function allowDuplicateSlugs() {
        $this->generateUniqueSlugs = false;
        return $this;
    }

    public function slugsShouldBeNoLongerThan($maximumLength) {
        $this->maximumLength = $maximumLength;
        return $this;
    }

    public function doNotGenerateSlugsOnCreate() {
        $this->generateSlugsOnCreate = false;
        return $this;
    }

    public function doNotGenerateSlugsOnUpdate() {
        $this->generateSlugsOnUpdate = false;
        return $this;
    }

    public function usingSeparator($separator) {
        $this->slugSeparator = $separator;
        return $this;
    }

    public function usingLanguage($language) {
        $this->slugLanguage = $language;
        return $this;
    }

}

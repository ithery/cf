<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 28, 2019, 9:48:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Search_SearchableAttribute {

    /** @var string */
    protected $attribute;

    /** @var bool */
    protected $partial;

    public function __construct($attribute, $partial = true) {
        $this->attribute = $attribute;
        $this->partial = $partial;
    }

    public static function create($attribute, $partial = true) {
        return new self($attribute, $partial);
    }

    public static function createExact($attribute) {
        return static::create($attribute, false);
    }

    public static function createMany(array $attributes) {
        return c::collect($attributes)
                        ->map(function ($attribute) {
                            return new self($attribute);
                        })
                        ->toArray();
    }

    public function getAttribute() {
        return $this->attribute;
    }

    public function isPartial() {
        return $this->partial;
    }

}

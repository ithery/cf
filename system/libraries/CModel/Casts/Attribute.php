<?php

class CModel_Casts_Attribute {
    /**
     * The attribute accessor.
     *
     * @var callable
     */
    public $get;

    /**
     * The attribute mutator.
     *
     * @var callable
     */
    public $set;

    /**
     * Indicates if caching of objects is enabled for this attribute.
     *
     * @var bool
     */
    public $withObjectCaching = true;

    /**
     * Create a new attribute accessor / mutator.
     *
     * @param null|callable $get
     * @param null|callable $set
     *
     * @return void
     */
    public function __construct($get = null, $set = null) {
        $this->get = $get;
        $this->set = $set;
    }

    /**
     * Create a new attribute accessor / mutator.
     *
     * @param null|callable $get
     * @param null|callable $set
     *
     * @return static
     */
    public static function make($get = null, $set = null) {
        return new static($get, $set);
    }

    /**
     * Create a new attribute accessor.
     *
     * @param callable $get
     *
     * @return static
     */
    public static function get($get) {
        return new static($get);
    }

    /**
     * Create a new attribute mutator.
     *
     * @param callable $set
     *
     * @return static
     */
    public static function set($set) {
        return new static(null, $set);
    }

    /**
     * Disable object caching for the attribute.
     *
     * @return static
     */
    public function withoutObjectCaching() {
        $this->withObjectCaching = false;

        return $this;
    }
}

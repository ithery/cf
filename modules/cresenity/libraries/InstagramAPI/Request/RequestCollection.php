<?php

/**
 * Base class for grouping multiple related request functions.
 */
class InstagramAPI_Request_RequestCollection {

    /** @var Instagram The parent class instance we belong to. */
    public $ig;

    /**
     * Constructor.
     *
     * @param Instagram $parent The parent class instance we belong to.
     */
    public function __construct($parent) {
        $this->ig = $parent;
    }

}

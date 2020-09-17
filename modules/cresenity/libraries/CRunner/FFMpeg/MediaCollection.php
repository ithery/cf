<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Aug 26, 2020 
 * @license Ittron Global Teknologi
 */

/**
 * @mixin CCollection
 */
class CRunner_FFMpeg_MediaCollection {

    use CTrait_ForwardsCalls;

    /**
     * @var CCollection
     */
    private $items;

    public function __construct(array $items = []) {
        $this->items = new CCollection($items);
    }

    public static function make(array $items = []) {
        return new static($items);
    }

    /**
     * Returns an array with all locals paths of the Media items.
     */
    public function getLocalPaths() {
        return $this->items->map->getLocalPath()->all();
    }

    public function collection() {
        return $this->items;
    }

    public function __call($method, $parameters) {
        return $this->forwardCallTo($this->collection(), $method, $parameters);
    }

}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * This is the interface that is always return from a Geocoder.
 */
interface CGeo_Interface_CollectionInterface extends \IteratorAggregate, \Countable {
    /**
     * @throws CGeo_Exception_CollectionIsEmpty
     *
     * @return CGeo_Location
     */
    public function first();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @param mixed      $offset
     * @param null|mixed $length
     *
     * @return CGeo_Location[]
     */
    public function slice($offset, $length = null);

    /**
     * @param mixed $index
     *
     * @return bool
     */
    public function has($index);

    /**
     * @param mixed $index
     *
     * @throws CGeo_Exception_OutOfBounds
     *
     * @return CGeo_Location
     */
    public function get($index);

    /**
     * @return CGeo_Location[]
     */
    public function all();
}

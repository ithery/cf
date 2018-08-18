<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 7:54:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * This is the interface that is always return from a Geocoder.
 */
interface CGeo_Interface_CollectionInterface extends \IteratorAggregate, \Countable {

    /**
     * @return CGeo_Location
     *
     * @throws CollectionIsEmpty
     */
    public function first();

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return CGeo_Location[]
     */
    public function slice($offset, $length = null);

    /**
     * @return bool
     */
    public function has($index);

    /**
     * @return CGeo_Location
     *
     * @throws OutOfBounds
     */
    public function get($index);

    /**
     * @return CGeo_Location[]
     */
    public function all();
}

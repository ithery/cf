<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 18, 2018, 7:54:01 PM
 */

/**
 * This is the interface that is always return from a Geocoder.
 */
interface CGeo_Interface_CollectionInterface extends \IteratorAggregate, \Countable {
    /**
     * @return CGeo_Location
     *
     * @throws CGeo_Exception_CollectionIsEmpty
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
     * @return CGeo_Location
     *
     * @throws CGeo_Exception_OutOfBounds
     */
    public function get($index);

    /**
     * @return CGeo_Location[]
     */
    public function all();
}

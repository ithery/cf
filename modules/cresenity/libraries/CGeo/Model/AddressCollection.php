<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 9:10:32 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CGeo_Model_AddressCollection implements CGeo_Interface_CollectionInterface {

    /**
     * @var Location[]
     */
    private $locations;

    /**
     * @param Location[] $locations
     */
    public function __construct(array $locations = []) {
        $this->locations = array_values($locations);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() {
        return new \ArrayIterator($this->all());
    }

    /**
     * {@inheritdoc}
     */
    public function count() {
        return count($this->locations);
    }

    /**
     * {@inheritdoc}
     */
    public function first() {
        if (empty($this->locations)) {
            throw new CGeo_Exception_CollectionIsEmpty();
        }
        return reset($this->locations);
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty() {
        return empty($this->locations);
    }

    /**
     * @return CGeo_Interface_LocationInterface[]
     */
    public function slice($offset, $length = null) {
        return array_slice($this->locations, $offset, $length);
    }

    /**
     * @return bool
     */
    public function has($index) {
        return isset($this->locations[$index]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($index) {
        if (!isset($this->locations[$index])) {
            throw new CGeo_Exception_OutOfBounds(sprintf('The index "%s" does not exist in this collection.', $index));
        }
        return $this->locations[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function all() {
        return $this->locations;
    }

}

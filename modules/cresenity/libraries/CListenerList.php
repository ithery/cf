<?php

/**
 * Provides functionality to manage a Listener Collection
 * 
 * @package Cresenity
 */
class CListenerList extends CAbstractList {

    public function __construct($collection = array()) {
        parent::__construct($collection);
    }

    /**
     * Adds a Listener to the Collection
     *
     * @param Listener $object
     * @param string $eventName The event name linked to the listener
     * @return int the index of the new element
     */
    public function add(CListener $object, $event) {
        return $parent->add($object, $eventName);
    }

}

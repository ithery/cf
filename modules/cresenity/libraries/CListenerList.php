<?php


/**
 * Provides functionality to manage a PhpExt_Listener Collection
 * 
 * @package PhpExt
 */
class CListenerList extends CAbstractList 
{
	
	public function __construct($collection = array()) {
		parent::__construct($collection);			
	}
	
	/**
	 * Adds a PhpExt_Listener to the Collection
	 *
	 * @param PhpExt_Listener $object
	 * @param string $eventName The event name linked to the listener
	 * @return int the index of the new element
	 */
	public function add(CListener $object, $event) {
		return $parent->add($object, $eventName);
	}
	
	
}



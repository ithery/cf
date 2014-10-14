<?php

abstract class CAbstractList 
{
	/**
	 * collection Container
	 *
	 * @var array	 
	 */
	public $collection = array();
	
	/**
	 * True to force the collection to render as array when now elements present.
	 *
	 * @var boolean
	 */
	protected $_force_array = false;
	
	/**
	 * True to force the collection to render as array when now elements present.
	 *
	 * @param boolean $value
	 */
	public function set_force_array($value) {
	    $this->_force_array = $value;
	}
	
	/**
	 * True to force the collection to render as array when now elements present.
	 *
	 * @return boolean
	 */
	public function get_force_array() {
	    return $this->_force_array;
	}
	
	
	public function __construct($collection = array()) {
		$this->collection = $collection;	
	}
	
	protected function add($object, $name = null) {
		if ($name !== null)
			$this->collection[$name] =& $object;
		else
			$this->collection[] =& $object;
		return $this;
	}
	
	public function remove($name) {
	    unset($this->collection[$name]);
	}
	
	protected function get_by_name($name) {
		if (array_key_exists($name, $this->collection))
			return $this->collection[$name];
		return null;  
	}
	
	protected function get_by_index($index) {
		if ($index < count($this->collection) )
			return $this->collection[$index];
		return null;  
	}
	
	public function length() {
		return count($this->collection);
	}
	
	public function js() {
		$resolvedObjs = array();		
		foreach($this->collection as &$obj) {			
			$resolvedObjs[] = CJS::js($obj, true);
		}
		if (count($resolvedObjs) == 1 && !$this->_force_array)
			return $resolvedObjs[0];
		else
			return "[".implode(",",$resolvedObjs)."]";
	}
	
	static public function is_instanceof($value) {
		if (is_object($value)) {
			return ($value instanceof CAbstractList);
		}
		return false;
	}
}



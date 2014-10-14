<?php

class CJDBResult implements ArrayAccess, Iterator, Countable {
	
	private $data = array();
	// Current and total rows
	protected $current_row = 0;
	protected $total_rows  = 0;
	
	public function __construct($data) {
		$this->data = $data;
		$this->data_object = json_decode(json_encode($data));
		$this->total_rows = count($data);
	}
	
	public static function factory($data) {
		return new CJDBResult($data);
	}
	
	public function result($object=true) {
		if($object) return $this->data_object;
		return $this->data;
	}
	public function result_array() {
		return $this->result(false);
	}
	
	/**
	 * Countable: count
	 */
	public function count() {
		return $this->total_rows;
	}

	/**
	 * ArrayAccess: offsetExists
	 */
	public function offsetExists($offset) {
		if ($this->total_rows > 0) {
			$min = 0;
			$max = $this->total_rows - 1;

			return ! ($offset < $min OR $offset > $max);
		}

		return FALSE;
	}
	/**
	 * ArrayAccess: offsetGet
	 */
	public function offsetGet($offset) {
		return $this->data_object[$offset];
		
	}
	
	/**
	 * ArrayAccess: offsetSet
	 *
	 * @throws  Exception
	 */
	public function offsetSet($offset, $value) {
		throw new CException('CJDBResult read only');
	}
	/**
	 * ArrayAccess: offsetUnset
	 *
	 * @throws  Exception
	 */
	public function offsetUnset($offset) {
		throw new CException('CJDBResult read only');
	}
	
	/**
	 * Iterator: current
	 */
	public function current() {
		return $this->offsetGet($this->current_row);
	}
	/**
	 * Iterator: key
	 */
	public function key() {
		return $this->current_row;
	}

	/**
	 * Iterator: next
	 */
	public function next() {
		++$this->current_row;
		return $this;
	}

	/**
	 * Iterator: prev
	 */
	public function prev() {
		--$this->current_row;
		return $this;
	}

	/**
	 * Iterator: rewind
	 */
	public function rewind() {
		$this->current_row = 0;
		return $this;
	}

	/**
	 * Iterator: valid
	 */
	public function valid() {
		return $this->offsetExists($this->current_row);
	}
}
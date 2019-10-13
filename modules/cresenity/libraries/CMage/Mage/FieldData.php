<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CMage_Mage_FieldData implements ArrayAccess, Iterator, Countable {
    protected $fields;
    protected $mage;
    /**
     *
     * @var int 
     */
    protected $currentOffset = 0;
    
    public function __construct($mage) {
        $this->mage=$mage;
        $this->fields = [];
    }
    
    public function addField($name) {
        $field = new CMage_Field($name);
        $this->fields[]=$field;
        return $field;
    }

    public function count() {
        return count($this->fields);
    }

    public function current() {
        return $this->offsetGet($this->currentOffset);
    }

    public function key() {
        return $this->currentOffset;
    }

    
    public function prev() {
        --$this->currentOffset;
        return $this;
    }
    public function next() {
        ++$this->currentOffset;
        return $this;
    }

    public function offsetExists($offset) {
        return isset($this->fields[$offset]);
    }

    public function offsetGet($offset) {
        if($this->offsetExists($offset)) {
            return $this->fields[$offset];
        }
       
    }

    public function offsetSet($offset, $value) {
        throw new Exception('fields is readonly');
    }

    public function offsetUnset($offset) {
        throw new Exception('fields is readonly');
    }

    public function rewind() {
        $this->currentOffset = 0;
        return $this;
    }

    public function valid() {
        return $this->offsetExists($this->currentOffset);
    }

}
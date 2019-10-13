<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CMage_Mage_FilterData implements ArrayAccess, Iterator, Countable {
    protected $filters;
    protected $mage;
    /**
     *
     * @var int 
     */
    protected $currentOffset = 0;
    
    public function __construct($mage) {
        $this->mage=$mage;
        $this->filters = [];
    }
    
    public function addFilter($name) {
        $field = new CMage_Filter($name);
        $this->fields[]=$field;
        return $field;
    }

    public function count() {
        return count($this->filters);
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
        return isset($this->filters[$offset]);
    }

    public function offsetGet($offset) {
        if($this->offsetExists($offset)) {
            return $this->filters[$offset];
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
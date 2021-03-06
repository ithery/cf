<?php

/**
 * @deprecated since 1.3 dont use this
 */
//@codingStandardsIgnoreStart
class CList extends CAbstractList {
    public function add($name, $object = null) {
        return parent::add($object, $name);
    }

    public function get_by_name($name) {
        if (array_key_exists($name, $this->collection)) {
            return $this->collection[$name];
        }
        return null;
    }

    public function get_by_index($index) {
        if ($index < count($this->collection)) {
            return $this->collection[$index];
        }
        return null;
    }

    public function length() {
        return count($this->collection);
    }

    public function get_list() {
        return $this->collection;
    }

    public function js() {
        $resolvedObjs = [];
        foreach ($this->collection as &$obj) {
            //$resolvedObjs[] = CJS::js($obj, true);
        }
        if (count($resolvedObjs) == 1 && !$this->_force_array) {
            return $resolvedObjs[0];
        } else {
            return '[' . implode(',', $resolvedObjs) . ']';
        }
    }
}

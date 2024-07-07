<?php

class CReport_Jasper_Report_DataRow implements ArrayAccess {
    protected $row;

    public function __construct($row) {
        $this->row = $row;
    }

    /**
     * ArrayAccess: offsetExists.
     *
     * @param mixed $offset
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($offset) {
        if (is_array($this->row)) {
            return isset($this->row[$offset]);
        }
        if ($this->row instanceof CCollection) {
            return $this->row->has($offset);
        }
        if ($this->row instanceof CModel) {
            return isset($this->row->getAttributes()[$offset]);
        }

        return false;
    }

    /**
     * ArrayAccess: offsetGet.
     *
     * @param mixed $offset
     */
    public function offsetGet($offset) {
        return carr::get($this->row, $offset);
    }

    /**
     * ArrayAccess: offsetSet.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value) {
        throw new Exception('CReport_Jasper_Report_DataRow data is read only');
    }

    /**
     * ArrayAccess: offsetUnset.
     *
     * @param mixed $offset
     *
     * @throws Exception
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset) {
        throw new Exception('CReport_Jasper_Report_DataRow data is read only');
    }

    public function __get($name) {
        if ($this->row instanceof CModel) {
            return carr::get($this->row->getAttributes(), $name);
        }
        if ($this->row instanceof CCollection) {
            return $this->row->get($name);
        }
        if (is_array($this->row)) {
            return carr::get($this->row, $name);
        }

        return null;
    }

    /**
     * @return array
     */
    public function toArray() {
        if ($this->row instanceof CMOdel) {
            return $this->row->getAttributes();
        }
        if ($this->row instanceof CCollection) {
            return $this->row->toArray();
        }
        if (is_array($this->row)) {
            return $this->row;
        }

        return [];
    }
}

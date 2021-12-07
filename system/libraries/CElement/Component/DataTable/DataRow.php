<?php

class CElement_Component_DataTable_DataRow implements CInterface_Arrayable {
    protected $row;

    public function __construct($row) {
        $this->row = $row;
    }

    public function getValue($field) {
        if ($this->row instanceof CModel) {
            return array_reduce(
                explode('.', $field),
                function ($o, $p) {
                    return $o->$p;
                },
                $this->row
            );
        }
        if (carr::accessible($this->row)) {
            return carr::get($this->row, $field);
        }

        return $field;
    }

    public function toArray() {
        if ($this->row instanceof CModel) {
            return $this->row->getAttributes();
        }

        return (array) $this->row;
    }
}

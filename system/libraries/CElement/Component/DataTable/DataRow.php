<?php

use Illuminate\Contracts\Support\Arrayable;

class CElement_Component_DataTable_DataRow implements Arrayable {
    /**
     * @var mixed
     */
    protected $row;

    /**
     * @param mixed $row
     *
     * @return void
     */
    public function __construct($row) {
        $this->row = $row;
    }

    /**
     * @return mixed
     */
    public function getRow() {
        return $this->row;
    }

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function getValue($field) {
        if ($this->row instanceof CModel) {
            return array_reduce(
                explode('.', $field),
                function ($o, $p) {
                    return c::optional($o)->$p;
                },
                $this->row
            );
        }
        if (carr::accessible($this->row)) {
            return carr::get($this->row, $field);
        }

        return $field;
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    public function exists($field) {
        if ($this->row instanceof CModel) {
            return isset($this->row->$field);
        }
        if (carr::accessible($this->row)) {
            return carr::exists($this->row, $field);
        }

        return $field;
    }

    /**
     * @return array
     */
    public function toArray() {
        if ($this->row instanceof CModel) {
            return $this->row->getAttributes();
        }

        return (array) $this->row;
    }
}

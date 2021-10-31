<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Validator_Failure implements CInterface_Arrayable {

    /**
     * @var int
     */
    protected $row;

    /**
     * @var string
     */
    protected $attribute;

    /**
     * @var array
     */
    protected $errors;

    /**
     * @var array
     */
    private $values;

    /**
     * @param int    $row
     * @param string $attribute
     * @param array  $errors
     * @param array  $values
     */
    public function __construct($row, $attribute, array $errors, array $values = []) {
        $this->row = $row;
        $this->attribute = $attribute;
        $this->errors = $errors;
        $this->values = $values;
    }

    /**
     * @return int
     */
    public function row() {
        return $this->row;
    }

    /**
     * @return string
     */
    public function attribute() {
        return $this->attribute;
    }

    /**
     * @return array
     */
    public function errors() {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function values() {
        return $this->values;
    }

    /**
     * @return array
     */
    public function toArray() {
        return c::collect($this->errors)->map(function ($message) {
                    return __('There was an error on row :row. :message', ['row' => $this->row, 'message' => $message]);
                })->all();
    }

}

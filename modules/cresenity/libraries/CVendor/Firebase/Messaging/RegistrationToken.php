<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CVendor_Firebase_Messaging_RegistrationToken implements \JsonSerializable {

    /**
     * @var string
     */
    private $value;

    private function __construct($value) {
        $this->value = $value;
    }

    public static function fromValue($value) {
        return new self($value);
    }

    public function value() {
        return $this->value;
    }

    public function __toString() {
        
        return $this->value;
    }

    public function jsonSerialize() {
        return $this->value;
    }

}

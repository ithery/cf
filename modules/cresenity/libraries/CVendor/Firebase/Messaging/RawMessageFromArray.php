<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

final class CVendor_Firebase_Messaging_RawMessageFromArray implements CVendor_Firebase_Messaging_MessageInterface {

    /** @var array */
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function jsonSerialize() {
        return $this->data;
    }

}

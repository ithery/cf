<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use SuperClosure\Serializer;

trait CJob_SerializerTrait {

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @return Serializer
     */
    protected function getSerializer() {
        if ($this->serializer === null) {
            $this->serializer = new Serializer();
        }
        return $this->serializer;
    }

}

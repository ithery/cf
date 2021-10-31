<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cresenity\Base\Contract;

interface Arrayable {

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}

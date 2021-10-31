<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class CExporter_Event {

    /**
     * @return object
     */
    abstract public function getConcernable();

    /**
     * @return mixed
     */
    abstract public function getDelegate();

    /**
     * @param string $concern
     *
     * @return bool
     */
    public function appliesToConcern($concern) {
        return $this->getConcernable() instanceof $concern;
    }

}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CExporter_Event_ImportFailed {

    /**
     * @var Throwable
     */
    public $e;

    /**
     * @param  Throwable  $e
     */
    public function __construct(Throwable $e) {
        $this->e = $e;
    }

    /**
     * @return Throwable
     */
    public function getException() {
        return $this->e;
    }

}

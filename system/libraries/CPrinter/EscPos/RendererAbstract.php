<?php

abstract class CPrinter_EscPos_RendererAbstract {
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }
}

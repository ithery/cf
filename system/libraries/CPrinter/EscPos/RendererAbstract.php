<?php

abstract class CPrinter_EscPos_RendererAbstract {
    protected $data;

    protected $profile;

    public function __construct($data, $profile = null) {
        $this->data = $data;
        $this->profile = $profile;
    }
}

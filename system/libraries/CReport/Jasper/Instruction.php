<?php

class CReport_Jasper_Instruction {
    protected $type;

    protected $params;

    public function __construct($type, $params) {
        $this->type = $type;
        $this->params = $params;
    }
}

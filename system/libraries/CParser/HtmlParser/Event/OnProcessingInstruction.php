<?php

class CParser_HtmlParser_Event_OnProcessingInstruction {
    public $name;
    public $instruction;

    public function __construct($name, $instruction) {
        $this->name = $name;
        $this->instruction = $instruction;
    }
}

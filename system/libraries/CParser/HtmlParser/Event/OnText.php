<?php

class CParser_HtmlParser_Event_OnText {
    public $value;

    public function __construct($value) {
        $this->value = $value;
    }
}

<?php

class CParser_HtmlParser_Event_OnOpenTag {
    public $name;
    public $attributes;

    public function __construct($name, $attributes) {
        $this->name = $name;
        $this->attributes = $attributes;
    }
}

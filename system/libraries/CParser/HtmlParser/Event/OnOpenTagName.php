<?php

class CParser_HtmlParser_Event_OnOpenTagName {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }
}

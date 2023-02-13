<?php

class CParser_HtmlParser_Event_OnCloseTag {
    public $name;

    public function __construct($name) {
        $this->name = $name;
    }
}

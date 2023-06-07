<?php

class CParser_HtmlParser_Event_OnComment {
    public $data;

    public function __construct($data) {
        $this->data = $data;
    }
}

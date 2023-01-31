<?php

class CParser_HtmlParser_Event_OnError {
    public $exception;

    public function __construct($exception) {
        $this->exception = $exception;
    }
}

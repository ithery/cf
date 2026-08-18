<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_Pseudo_CloseListener extends CObservable_PseudoListener {
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'close';
        $this->eventParameters = ['e'];
    }
}

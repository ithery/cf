<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_Pseudo_ErrorListener extends CObservable_PseudoListener {
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'ajaxError';
    }
}

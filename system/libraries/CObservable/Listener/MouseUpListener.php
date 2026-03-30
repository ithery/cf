<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_MouseUpListener extends CObservable_Listener {
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'mouseup';
    }
}

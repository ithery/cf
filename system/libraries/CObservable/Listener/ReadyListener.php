<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_ReadyListener extends CObservable_Listener {
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'ready';
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_ClickListener extends CObservable_Listener {
    /**
     * Constructor.
     *
     * @param CObservable $owner
     *
     * @return void
     */
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'click';
    }
}

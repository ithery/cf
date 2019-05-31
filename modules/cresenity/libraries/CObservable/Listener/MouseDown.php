<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 3:12:36 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

class CObservable_Listener_MouseDownListener extends CObservable_Listener {

    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'mousedown';
    }

}

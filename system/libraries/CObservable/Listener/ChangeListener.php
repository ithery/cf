<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 20, 2019, 3:11:48 PM
 */
class CObservable_Listener_ChangeListener extends CObservable_Listener {
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'change';
    }
}

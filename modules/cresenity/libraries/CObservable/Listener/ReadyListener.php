<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 3, 2021, 1:08:44 PM
 */
class CObservable_Listener_ReadyListener extends CObservable_Listener {
    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'ready';
    }
}

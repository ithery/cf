<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 5:42:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Pseudo_CompleteListener extends CObservable_PseudoListener {

    public function __construct($owner) {
        parent::__construct($owner);
        $this->event = 'ajaxComplete';
    }

}

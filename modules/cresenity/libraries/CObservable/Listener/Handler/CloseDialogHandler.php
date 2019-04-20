<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 20, 2019, 6:50:11 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CObservable_Listener_Handler_CloseDialogHandler extends CObservable_Listener_Handler {

    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Custom';
    }

    public function js() {
        $js = '';
        $js = 'cresenity.closeDialog();';

        return $js;
    }

}

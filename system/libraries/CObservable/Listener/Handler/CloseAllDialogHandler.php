<?php

defined('SYSPATH') or die('No direct access allowed.');

class CObservable_Listener_Handler_CloseAllDialogHandler extends CObservable_Listener_Handler {
    public function __construct($listener) {
        parent::__construct($listener);

        $this->name = 'Custom';
    }

    public function js() {
        $js = '';
        $js = 'cresenity.closeAllDialog();';

        return $js;
    }
}

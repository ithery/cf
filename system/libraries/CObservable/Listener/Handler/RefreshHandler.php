<?php

class CObservable_Listener_Handler_RefreshHandler extends CObservable_Listener_Handler {
    public function __construct($listener) {
        parent::__construct($listener);
    }

    public function js() {
        $js = 'window.location.reload();';

        return $js;
    }
}

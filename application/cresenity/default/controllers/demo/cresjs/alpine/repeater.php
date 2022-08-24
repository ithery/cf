<?php

class Controller_Demo_Cresjs_Alpine_Repeater extends \Cresenity\Demo\Controller {
    // START_REMOVE_PREVIEW

    // END_REMOVE_PREVIEW

    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->addView('demo.page.cresjs.alpine.repeater');

        return $app;
    }
}

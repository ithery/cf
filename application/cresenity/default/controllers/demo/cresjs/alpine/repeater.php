<?php

class Controller_Demo_Cresjs_Alpine_Repeater extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Repeater');

        $app->addView('demo.page.cresjs.alpine.repeater');

        return $app;
    }
}

<?php

class Controller_Demo_Cresjs_Alpine_Select2 extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Select2');

        $app->addView('demo.page.cresjs.alpine.select2');

        return $app;
    }
}

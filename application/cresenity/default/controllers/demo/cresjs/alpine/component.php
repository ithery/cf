<?php

class Controller_Demo_Cresjs_Alpine_Component extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Component');
        $data = [
        ];
        $app->addView('demo.page.cresjs.alpine.component', $data);

        return $app;
    }
}

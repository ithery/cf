<?php

class Controller_Demo_Cresjs_Alpine_Control extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Controls');
        $data = [
            'autoNumericValue' => 1234.56
        ];
        c::manager()->registerModule('auto-numeric');
        $app->addView('demo.page.cresjs.alpine.control', $data);

        return $app;
    }
}

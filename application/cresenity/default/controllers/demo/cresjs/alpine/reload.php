<?php

class Controller_Demo_Cresjs_Alpine_Reload extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Reload');
        $data = [
        ];
        $app->addView('demo.page.cresjs.alpine.reload', $data);

        return $app;
    }

    public function reload($value) {
        $app = c::app();
        $app->addDiv()->add('Value:' . $value);

        return $app;
    }
}

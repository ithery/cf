<?php

class Controller_Demo_App_Javascript extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Javascript');
        $div = $app->addDiv();

        $js = $div->javascript();
        $js->jquery()->html('Written And modified by Javascript');
        $js->jquery()->addClass('text-success');

        $button = $app->addAction();
        $button->onClick(function (CObservable_Javascript $js) {
            $js->jquery()->remove();
        })->setLabel('Click Me To Remove Me')->addClass('btn-primary');

        return $app;
    }
}

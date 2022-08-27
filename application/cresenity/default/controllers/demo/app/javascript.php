<?php

class Controller_Demo_App_Javascript extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Javascript');
        $div = $app->addDiv();

        $js = $div->javascript();
        $js->jquery()->html('Written And modified by Javascript');
        $js->jquery()->addClass('text-success');
        $app->addDiv()->addClass('border-1 py-3');
        $button = $app->addAction();
        $button->onClick(function (CObservable_Javascript $js) {
            $js->jquery()->remove();
        })->setLabel('Click Me To Remove Me')->addClass('btn-primary');

        $app->addDiv()->addClass('border-1 py-3');

        $button = $app->addAction()->addClass('mb-3');

        $divText = $app->addDiv();
        $divText->add('Hello World');
        $divText->customCss('display', 'none');
        $button->onClick(function (CObservable_Javascript $js) use ($divText) {
            $js->jquery($divText)->toggle();
        })->setLabel('Click Me To Show Something')->addClass('btn-primary');

        return $app;
    }
}

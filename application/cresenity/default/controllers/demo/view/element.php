<?php

class Controller_Demo_View_Element extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('View | Element');

        $view = $app->addView('demo.page.view.element.index', [
            'foo' => 'bar'
        ]);

        $myElement = $view->viewElement('myElement');
        $myElement->add('Rendered from controller');

        return $app;
    }
}

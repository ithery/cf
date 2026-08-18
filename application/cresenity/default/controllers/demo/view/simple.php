<?php

class Controller_Demo_View_Simple extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('View | Simple');

        $app->addView('demo.page.view.simple.index', [
            'foo' => 'bar'
        ]);

        return $app;
    }
}

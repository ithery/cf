<?php

class Controller_Demo_Elements_Widget extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Widget');
        $widget = $app->addWidget()->setTitle('Widget Title');
        $widget->add('Widget Content Here');

        $app->addDiv()->addClass('border-1 p-3 mb-3');
        $app->addH5()->add('Widget With Action');

        $widget = $app->addWidget()->setTitle('Widget With Action');
        $widget->addHeaderAction()->setLabel('Some Action')->addClass('btn-primary');
        $widget->add('Widget Content Here');

        $app->addDiv()->addClass('border-1 p-3 mb-3');
        $app->addH5()->add('Widget With Action Dropdown');

        $widget = $app->addWidget()->setTitle('Widget With Action Dropdown');
        $widget->addHeaderAction()->setLabel('Action 1');
        $widget->addHeaderAction()->setLabel('Action 2');
        $widget->setHeaderActionStyle('btn-dropdown');
        $widget->add('Widget Content Here');

        return $app;
    }
}

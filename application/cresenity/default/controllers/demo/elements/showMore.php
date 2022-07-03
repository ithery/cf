<?php

class Controller_Demo_Elements_ShowMore extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Show More');

        $widget = $app->addWidget()->setTitle('Show More Demo');
        $widget->addShowMore()->setLimit(50)->add('Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');

        return $app;
    }
}

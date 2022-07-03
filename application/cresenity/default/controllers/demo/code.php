<?php

class Controller_Demo_Code extends \Cresenity\Demo\Controller {
    public function show() {
        $app = c::app();

        $uri = c::request()->query('uri');

        //validate uri
        if (!cstr::startsWith($uri, 'demo/')) {
            return c::abort(404);
        }
        $routeFinder = new CRouting_RouteFinder();
        $route = $routeFinder->find($uri);

        if ($route) {
            $controllerPath = $route->getRouteData()->getControllerPath();
            $code = CFile::get($controllerPath);
            $div = $app->addDiv()->addClass('prism-container');
            $div->addPrismCode()->add(c::e($code));
        }

        return $app;
    }
}

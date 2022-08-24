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

            //we will regex all views
            preg_match_all('#\$app->addView\(\'(.+?)\'\);#ims', $code, $matches);
            if (is_array($matches)) {
                foreach ($matches[1] as $view) {
                    $viewPath = CView_Finder::instance()->find($view);
                    $div->addH5()->addClass('mt-4')->add($view);
                    $viewCode = CFile::get($viewPath);
                    $div->addPrismCode()->add(c::e($viewCode));
                }
            }
        }

        return $app;
    }
}

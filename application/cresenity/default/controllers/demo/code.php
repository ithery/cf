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
            preg_match_all('#->addView\(\'(.+?)\'\);#ims', $code, $matches);
            if (is_array($matches)) {
                foreach ($matches[1] as $view) {
                    $viewPath = CView_Finder::instance()->find($view);
                    $div->addH5()->addClass('mt-4')->add($view);
                    $viewCode = CFile::get($viewPath);
                    $div->addPrismCode()->add(c::e($viewCode));
                }
            }

            //we will regex all model
            preg_match_all('#->setDataFromModel\((.+?)\:\:class\);#ims', $code, $matches);
            if (is_array($matches)) {
                foreach ($matches[1] as $class) {
                    $filePath = CF::findFile('libraries', $class);

                    if (!$filePath) {
                        // Transform the class name according to PSR-0
                        $routingClass = ltrim($class, '\\');
                        $routingFile = '';
                        $namespace = '';

                        $isNamespace = false;
                        if ($lastNamespacePosition = strripos($routingClass, '\\')) {
                            $isNamespace = true;
                            $namespace = substr($routingClass, 0, $lastNamespacePosition);

                            $routingClass = substr($routingClass, $lastNamespacePosition + 1);
                            $routingFile = str_replace('\\', DS, $namespace) . DS;
                        }

                        $routingFile .= str_replace('_', DS, $routingClass);

                        if (substr($routingFile, strlen($routingFile) - 1, 1) == DS) {
                            $routingFile = substr($routingFile, 0, strlen($routingFile) - 1) . '_';
                        }
                        $filePath = CF::findFile('libraries', $routingFile);
                    }
                    if ($filePath) {
                        $div->addH5()->addClass('mt-4')->add($class);
                        $fileCode = CFile::get($filePath);
                        $div->addPrismCode()->add(c::e($fileCode));
                    }
                }
            }
        }

        return $app;
    }
}

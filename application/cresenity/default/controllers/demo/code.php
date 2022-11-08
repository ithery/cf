<?php

class Controller_Demo_Code extends \Cresenity\Demo\Controller {
    private $files = [];

    private function addCode($div, $type, $path) {
        $div->addH5()->addClass('mt-4')->add($type . ':' . basename($path));
        if (CFile::exists($path)) {
            $code = CFile::get($path);
            $div->addPrismCode()->add(c::e($code));
            if (!in_array($path, $this->files)) {
                $this->files[] = $path;
                $this->findingCode($div, $code);
            }
        } else {
            $div->add($type . ':' . $path . ' not found');
        }
    }

    private function findingCode($div, $code) {
        //we will regex all views
        preg_match_all('#->addView\(\'(.+?)\'#ims', $code, $matches);
        if (is_array($matches)) {
            foreach ($matches[1] as $view) {
                $viewPath = CView_Finder::instance()->find($view);
                $this->addCode($div, 'View', $viewPath);
            }
        }
        preg_match_all('#c::view\(\'(.+?)\'#ims', $code, $matches);
        if (is_array($matches)) {
            foreach ($matches[1] as $view) {
                $viewPath = CView_Finder::instance()->find($view);
                $this->addCode($div, 'View', $viewPath);
            }
        }
        preg_match_all('#@CAppReact\(\'(.+?)\'#ims', $code, $matches);
        if (is_array($matches)) {
            foreach ($matches[1] as $react) {
                $reactPath = CApp_React_Finder::instance()->find($react);
                $this->addCode($div, 'React', $reactPath);
            }
        }
        //we will regex all model
        preg_match_all('#->setDataFromModel\((.+?)\:\:class#ims', $code, $matches);
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
                $this->addCode($div, 'Model', $filePath);
            }
        }
    }

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
            $this->findingCode($div, $code);
        }

        return $app;
    }
}

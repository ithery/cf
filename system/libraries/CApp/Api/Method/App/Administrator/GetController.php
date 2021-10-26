<?php

class CApp_Api_Method_App_Administrator_GetController extends CApp_Api_Method_App {
    public function execute() {
        $appCode = $this->appCode;
        $controllerPath = DOCROOT . 'application/' . $appCode . '/default/controllers/';
        $controllerFiles = [];
        if (CFile::isDirectory($controllerPath)) {
            $allFiles = CFile::allFiles($controllerPath);
            foreach ($allFiles as $file) {
                $file = $file->__toString();
                $controllerFiles[] = $file;
            }
        }
        $data = [];
        foreach ($controllerFiles as $file) {
            $relativePath = str_replace($controllerPath, '', $file);
            if (cstr::endsWith($relativePath, '.php')) {
                $relativePath = substr($relativePath, 0, -4);
            }
            $controllerClass = 'Controller_' . str_replace('/', '_', $relativePath);
            require_once $file;
            $controller = new ReflectionClass($controllerClass);
            $controllerData = [];
            $controllerData['class'] = $controllerClass;
            $controllerData['methods'] = [];
            $methods = $controller->getMethods();
            foreach ($methods as $m) {
                $mData = [];
                $mData['name'] = $m->name;
                $mData['isPrivate'] = $m->isPrivate();
                $mData['isPublic'] = $m->isPublic();
                $mData['isPrivate'] = $m->isPrivate();
                $pData = [];
                $params = $m->getParameters();
                foreach ($params as $p) {
                    $pData[] = $p->getName();
                }
                $mData['params'] = $pData;
                $controlerData['methods'][] = $mData;
            }
            $data[$file] = $controlerData;
        }

        $this->data = $data;
    }
}

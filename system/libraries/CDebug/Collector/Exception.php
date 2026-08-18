<?php

class CDebug_Collector_Exception extends CDebug_CollectorAbstract {
    /**
     * @param Throwable $exception
     *
     * @return bool
     */
    protected function shouldCollect($exception) {
        return $exception instanceof Throwable && (!$exception instanceof CDebug_Contract_ShouldNotCollectException);
    }

    /**
     * @param Throwable $exception
     *
     * @return array
     */
    public function collect(Throwable $exception) {
        if (!CF::config('collector.exception')) {
            return null;
        }
        $data = null;
        if ($this->shouldCollect($exception)) {
            $data = $this->getDataFromException($exception);
            $this->put($data);
        }

        return $data;
    }

    /**
     * Get data from exception object.
     *
     * @param Throwable $exception
     *
     * @return array
     */
    public function getDataFromException($exception) {
        $app = CApp::instance();
        $route = c::request()->route();
        $routeData = [];
        if ($route && $route->getRouteData()) {
            $routeData = $route->getRouteData()->toArray();
        }
        $controller = c::optional($route)->controller;
        // Start validation of the controller
        $controllerClass = $controller ? get_class($controller) : null;
        $error = get_class($exception);
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTrace();
        $uuid = cstr::uuid();

        $browser = new CBrowser();
        $data = [];
        $data['datetime'] = date('Y-m-d H:i:s');
        $data['appId'] = $app->appId();
        $data['appCode'] = $app->code();
        $data['user'] = c::base()->username();
        $data['role'] = c::base()->roleName();
        $data['orgId'] = c::base()->orgId();
        $data['orgCode'] = c::base()->orgCode();
        $data['error'] = $error;
        $data['message'] = $message;
        $data['uuid'] = $uuid;
        $data['file'] = $file;
        $data['line'] = $line;
        $data['trace'] = json_encode($trace);
        $data['browser'] = $browser->getBrowser();
        $data['browserVersion'] = $browser->getVersion();
        $data['platform'] = $browser->getPlatform();
        $data['domain'] = CF::domain();
        $data['controller'] = $controllerClass;
        $data['method'] = carr::get($routeData, 'method');
        $data['userAgent'] = carr::get($_SERVER, 'HTTP_USER_AGENT');
        $data['httpReferer'] = carr::get($_SERVER, 'HTTP_REFERER');
        $data['remoteAddress'] = CApp_Base::remoteAddress();
        $data['fullUrl'] = curl::current();
        $data['protocol'] = CApp_Base::protocol();
        $data['CFVersion'] = CF::version();

        $report = CException::manager()->createReport($exception)->toArray();
        $data = array_merge($data, $report);

        return $data;
    }

    public function getType() {
        return CDebug::COLLECTOR_TYPE_EXCEPTION;
    }
}

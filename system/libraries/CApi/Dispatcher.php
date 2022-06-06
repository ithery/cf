<?php

class CApi_Dispatcher {
    use CApi_Trait_HasGroupPropertyTrait;

    protected $prefix;

    protected $methodNamespace;

    protected $isDispatching = false;

    public function __construct($group) {
        $this->group = $group;
        $this->prefix = CF::config('api.groups.' . $group . '.prefix', '');
        $this->methodNamespace = '';
    }

    public function setPrefix($prefix) {
        $this->prefix = trim($prefix, '/');

        return $this;
    }

    public function setMethodNamespace($methodNamespace) {
        $this->methodNamespace = $methodNamespace;

        return $this;
    }

    public function methodResolver() {
        return function (CHTTP_Request $request) {
            $path = $request->path();
            $originalPath = $path;

            if ($this->prefix && cstr::startsWith($path, $this->prefix)) {
                $path = cstr::substr($path, strlen($this->prefix));
            }
            $path = trim($path, '/');
            $nameSpaced = false;
            if ($this->methodNamespace && cstr::startsWith($this->methodNamespace, '\\')) {
                $nameSpaced = true;
            }
            $classPath = c::collect(explode('/', $path))->filter()->map(function ($item) {
                return cstr::ucfirst(cstr::camel($item));
            })->join($nameSpaced ? '\\' : '_');

            $class = $this->methodNamespace . ($nameSpaced ? '\\' : '_') . $classPath;
            if (!class_exists($class)) {
                throw new CApi_Exception_ApiMethodNotFoundException($originalPath . ' is not found');
            }

            return CApi_Factory::createMethod($class, $request);
        };
    }

    public function dispatch($request = null) {
        if ($request == null) {
            $request = c::request();
        }

        try {
            $this->isDispatching = true;
            CApi::setCurrentDispatcher($this);
            $request = CApi_HTTP_Request::createFromBaseHttp($request);
            $request->setGroup($this->group);
            $kernel = new CApi_Kernel($this->group);
            $response = $kernel->handle($request, $this->methodResolver());
        } catch (Exception $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        } finally {
            CApi::setCurrentDispatcher(null);
            $this->isDispatching = false;
        }

        return $response;
    }

    public function isDispatching() {
        return $this->isDispatching;
    }
}

<?php

class CApi_Manager {
    protected static $instance = [];

    /**
     * Api Group Parameter.
     *
     * @var string
     */
    private $group;

    /**
     * @var array
     */
    private $config;

    private $exceptionHandler;

    private $router;

    private $routerAdapter;

    private $dispatcher;

    private $auth;

    private $httpParseAccept;

    /**
     * @param string $group
     *
     * @return CApi_Manager
     */
    public static function instance($group = 'default') {
        if (!is_array(static::$instance)) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$group])) {
            static::$instance[$group] = new static($group);
        }

        return static::$instance[$group];
    }

    public function __construct($group) {
        $this->group = $group;
        $this->config = CF::config('api.' . $group, []);
    }

    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    /**
     * @return CApi_Contract_ExceptionHandlerInterface
     */
    public function exceptionHandler() {
        if ($this->exceptionHandler == null) {
            $this->exceptionHandler = new CApi_ExceptionHandler(
                $this->getConfig('error_format', [
                    'message' => ':message',
                    'errors' => ':errors',
                    'code' => ':code',
                    'status_code' => ':status_code',
                    'debug' => ':debug',
                ]),
                $this->getConfig('debug', !CF::isProduction())
            );
        }

        return $this->exceptionHandler;
    }

    /**
     * @return CApi_Contract_Routing_AdapterInterface
     */
    public function routerAdapter() {
        if ($this->routerAdapter == null) {
            $this->routerAdapter = new CApi_Routing_Adapter_DefaultAdapter(c::router());
        }

        return $this->routerAdapter;
    }

    /**
     * @return CApi_Routing_Router
     */
    public function router() {
        if ($this->router == null) {
            $this->router = new CApi_Routing_Router(
                $this->group,
                $this->routerAdapter(),
                $this->exceptionHandler(),
                $this->getConfig('domain', CF::domain()),
                $this->getConfig('prefix', ''),
            );
        }

        return $this->router;
    }

    public function auth() {
        if ($this->auth == null) {
            $this->auth = new CApi_Auth($this->router(), $this->getConfig('auth', []));
        }

        return $this->auth;
    }

    public function dispatcher() {
        if ($this->dispatcher == null) {
            $dispatcher = new CApi_Dispatcher($this->group, $this->router(), $this->auth());

            $dispatcher->setSubtype($this->getConfig('subtype', ''));
            $dispatcher->setStandardsTree($this->getConfig('standards_tree', 'x'));
            $dispatcher->setPrefix($this->getConfig('prefix', null));
            $dispatcher->setDefaultVersion($this->getConfig('version', 'v1'));
            $dispatcher->setDefaultDomain($this->getConfig('domain', CF::domain()));
            $dispatcher->setDefaultFormat($this->getConfig('default_format', 'json'));

            $this->dispatcher = $dispatcher;
        }

        return $this->dispatcher;
    }

    public function httpParseAccept() {
        if ($this->httpParseAccept == null) {
            $this->httpParseAccept = new CApi_HTTP_Parser_Accept(
                $this->getConfig('standards_tree', 'x'),
                $this->getConfig('subtype', ''),
                $this->getConfig('version', 'v1'),
                $this->getConfig('default_format', 'json')
            );
        }

        return $this->httpParseAccept;
    }
}

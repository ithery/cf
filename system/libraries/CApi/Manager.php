<?php
use League\Fractal\Manager as FractalManager;

class CApi_Manager {
    protected static $instance = [];

    protected $middlewareEnabled = true;

    protected $middleware = [];

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

    private $transformer;

    private $resultFormatter;

    /**
     * @param string $group
     *
     * @return CApi_Manager
     */
    public static function instance($group = null) {
        if ($group == null) {
            $group = CF::config('api.default');
        }
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
        $this->config = CF::config('api.groups.' . $group, []);
    }

    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    public function resultFormatter() {
        if ($this->resultFormatter == null) {
            $this->resultFormatter = new CApi_HTTP_Response_Format_JsonFormat();
        }

        return $this->resultFormatter;
    }

    public function transformer() {
        if ($this->transformer == null) {
            $transformerAdapter = new CApi_Transformer_Adapter_FractalAdapter(new FractalManager());
            $this->transformer = new CApi_Transformer_Factory($transformerAdapter);
        }

        return $this->transformer;
    }

    /**
     * @return CApi_ExceptionHandler
     */
    public function exceptionHandler() {
        if ($this->exceptionHandler == null) {
            $this->exceptionHandler = new CApi_ExceptionHandler(
                $this->getConfig('error_format', [
                    'errCode' => ':code',
                    'errMessage' => ':message',
                    'data' => [
                        'message' => ':message',
                        'errors' => ':errors',
                        'code' => ':code',
                        'status_code' => ':status_code',
                        'debug' => ':debug',
                    ]
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

    public function httpParseAccept() {
        if ($this->httpParseAccept == null) {
            $this->httpParseAccept = new CApi_HTTP_Parser_Accept(
                $this->getConfig('standards_tree', 'x'),
                $this->getConfig('subtype', ''),
                $this->getConfig('version', 'v1'),
                $this->getConfig('default_format', 'default')
            );
        }

        return $this->httpParseAccept;
    }

    public function getMiddleware() {
        return $this->middleware;
    }

    public function getMethodResolver() {
        return $this->methodResolver;
    }

    public function setMethodResolver($callback) {
        $this->methodResolver = $callback;

        return $this;
    }

    public function shouldSkipMiddleware() {
        return $this->middlewareEnabled;
    }

    protected function kernel() {
        return new CApi_Kernel($this->group);
    }

    public function createDispatcher() {
        return new CApi_Dispatcher($this->group);
    }
}

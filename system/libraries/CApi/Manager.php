<?php
use League\Fractal\Manager as FractalManager;

class CApi_Manager {
    /**
     * @var CApi_Manager[]
     */
    protected static $instance = [];

    /**
     * When true, overrides the per-instance $middlewareEnabled flag for every
     * group, including ones not created yet. Groups are instantiated lazily
     * (on first c::api($group) call), which can happen after a test has already
     * called withoutMiddleware() in setUp() — a per-instance-only flag would
     * silently miss those, so this needs to be checked independently of $instance.
     *
     * @var bool
     */
    protected static $globallyDisabled = false;

    /**
     * @var bool
     */
    protected $middlewareEnabled = true;

    /**
     * @var array
     */
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

    /**
     * @var CApi_ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var CApi_Routing_Router
     */
    private $router;

    /**
     * @var CApi_Contract_Routing_AdapterInterface
     */
    private $routerAdapter;

    /**
     * @var CApi_Dispatcher
     */
    private $dispatcher;

    /**
     * @var CApi_Auth
     */
    private $auth;

    /**
     * @var CApi_HTTP_Parser_Accept
     */
    private $httpParseAccept;

    /**
     * @var CApi_Transformer_Factory
     */
    private $transformer;

    /**
     * @var CApi_HTTP_Response_FormatAbstract
     */
    private $resultFormatter;

    /**
     * @var null|callable
     */
    private $methodResolver;

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

    /**
     * CApi_Manager constructor.
     *
     * @param string $group
     */
    public function __construct($group) {
        $this->group = $group;
        $this->config = CF::config('api.groups.' . $group, []);
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    /**
     * @return CApi_HTTP_Response_Format_JsonFormat
     */
    public function resultFormatter() {
        if ($this->resultFormatter == null) {
            $this->resultFormatter = new CApi_HTTP_Response_Format_JsonFormat();
        }

        return $this->resultFormatter;
    }

    /**
     * @return CApi_Transformer_Factory
     */
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
                $this->getConfig('prefix', '')
            );
        }

        return $this->router;
    }

    /**
     * @return CApi_Auth
     */
    public function auth() {
        if ($this->auth == null) {
            $this->auth = new CApi_Auth($this->router(), $this->getConfig('auth', []));
        }

        return $this->auth;
    }

    /**
     * @return CApi_HTTP_Parser_Accept
     */
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

    /**
     * @return array
     */
    public function getMiddleware() {
        return $this->middleware;
    }

    /**
     * @return null|callable
     */
    public function getMethodResolver() {
        return $this->methodResolver;
    }

    /**
     * @param callable $callback
     *
     * @return $this
     */
    public function setMethodResolver($callback) {
        $this->methodResolver = $callback;

        return $this;
    }

    /**
     * @return bool
     */
    public function shouldSkipMiddleware() {
        return static::$globallyDisabled || !$this->middlewareEnabled;
    }

    /**
     * Disable all middleware for this API group's requests (used in testing).
     *
     * @return $this
     */
    public function withoutMiddleware() {
        $this->middlewareEnabled = false;

        return $this;
    }

    /**
     * Re-enable middleware for this API group's requests.
     *
     * @return $this
     */
    public function withMiddleware() {
        $this->middlewareEnabled = true;

        return $this;
    }

    /**
     * Disable middleware for every API group, including ones not instantiated yet.
     *
     * @return void
     */
    public static function withoutMiddlewareForAllGroups() {
        static::$globallyDisabled = true;
    }

    /**
     * Re-enable middleware for every API group.
     *
     * @return void
     */
    public static function withMiddlewareForAllGroups() {
        static::$globallyDisabled = false;
    }

    /**
     * @return CApi_Kernel
     */
    protected function kernel() {
        return new CApi_Kernel($this->group);
    }

    /**
     * @return CApi_Dispatcher
     */
    public function createDispatcher() {
        return new CApi_Dispatcher($this->group);
    }

    /**
     * @return CApi_Docs_GeneratorFactory
     */
    public function createDocsGenerator() {
        return new CApi_Docs_GeneratorFactory($this->group);
    }

    // /**
    //  * @return CHTTP_RESPONSE
    //  */
    // public function createRunnerResponse() {
    //     $urlToDocs = $this->generateDocumentationFileURL($documentation, $config);
    //     $useAbsolutePath = config('l5-swagger.documentations.' . $documentation . '.paths.use_absolute_path', true);

    //     // Need the / at the end to avoid CORS errors on Homestead systems.
    //     return c::response()->view('cresenity.api.swagger', [
    //         'documentation' => $documentation,
    //         'secure' => c::request()->secure(),
    //         'urlToDocs' => $urlToDocs,
    //         'operationsSorter' => $config['operations_sort'],
    //         'configUrl' => $config['additional_config_url'],
    //         'validatorUrl' => $config['validator_url'],
    //         'useAbsolutePath' => $useAbsolutePath,
    //     ], 200);
    // }
}

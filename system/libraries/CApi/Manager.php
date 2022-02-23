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
        $this->config = CF::config('api.groups.' . $group, []);
    }

    public function getConfig($key, $default = null) {
        return carr::get($this->config, $key, $default);
    }

    public function formatters() {
        $formats = $this->getConfig('formats', []);

        return c::collect($formats)->map(function ($format) {
            return new $format();
        })->toArray();
    }

    public function transformer() {
        if ($this->transformer == null) {
            $transformerAdapterClass = $this->getConfig('transformer', CApi_Transformer_Adapter_FractalAdapter::class);
            $transformerAdapter = new $transformerAdapterClass(new FractalManager());
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

    /**
     * @return CApi_Dispatcher
     */
    public function dispatcher() {
        if ($this->dispatcher == null) {
            $dispatcher = new CApi_Dispatcher($this->group, $this->router(), $this->auth());

            $dispatcher->setSubtype($this->getConfig('subtype', ''));
            $dispatcher->setStandardsTree($this->getConfig('standards_tree', 'x'));
            $dispatcher->setPrefix($this->getConfig('prefix', null));
            $dispatcher->setDefaultVersion($this->getConfig('version', 'v1'));
            $dispatcher->setDefaultDomain($this->getConfig('domain', CF::domain()));
            $dispatcher->setDefaultFormat($this->getConfig('default_format', 'default'));

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
                $this->getConfig('default_format', 'default')
            );
        }

        return $this->httpParseAccept;
    }

    public function handle(CHTTP_Request $request) {
        try {
            $request = CApi_HTTP_Request::createFromBase($request);

            $response = $this->sendRequestThroughRouter($request);
        } catch (Exception $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {
            $this->reportException($e);
            $response = $this->renderException($request, $e);
        }

        CEvent::dispatch(new CApi_Event_RequestHandled($request, $response));
        //        if($response->getStatusCode()!=200) {
        //            $this->endOutputBuffering();
        //        }

        $this->isHandled = true;

        return $response;
    }

    /**
     * Send the given request through the middleware / router.
     *
     * @param \CApi_HTTP_Request $request
     *
     * @return \CApi_HTTP_Response
     */
    protected function sendRequestThroughRouter(CApi_HTTP_Request $request) {
        return (new CApi_HTTP_Pipeline())
            ->setGroup($this->group)
            ->send($request)
            ->through($this->shouldSkipMiddleware() ? [] : $this->getMiddleware())
            ->then($this->dispatchToRouter());
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter() {
        return function ($request) {
            return $this->router()->dispatch($request);
        };
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function reportException($e) {
        $this->exceptionHandler()->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param \CApi_HTTP_Request $request
     * @param \Exception         $e
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, $e) {
        return $this->exceptionHandler()->render($request, $e);
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
}

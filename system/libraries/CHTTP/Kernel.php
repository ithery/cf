<?php

/**
 * Description of Kernel
 *
 * @author Hery
 */
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;

class CHTTP_Kernel {

    use CHTTP_Trait_OutputBufferTrait;

    protected $isHandled = false;
    protected $terminated;
    protected $controller;

    public function __construct() {

        $this->terminated = false;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function reportException($e) {
        CException::exceptionHandler()->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, $e) {
        return CException::exceptionHandler()->render($request, $e);
    }

    public function setupRouter() {
        CFRouter::findUri();
        CFRouter::setup();
    }

    /**
     * 
     * @return ReflectionClass
     * @throws ReflectionException
     */
    public function getReflectionControllerClass() {

        CFBenchmark::start(SYSTEM_BENCHMARK . '_controller_setup');
        $reflectionClass = null;
        // Include the Controller file
        if (strlen(CFRouter::$controller_path) > 0) {
            require_once CFRouter::$controller_path;



            try {
                // Start validation of the controller
                $className = str_replace('/', '_', CFRouter::$controller_dir_ucfirst);
                $reflectionClass = new ReflectionClass('Controller_' . $className . ucfirst(CFRouter::$controller));
            } catch (ReflectionException $e) {
                try {
                    $reflectionClass = new ReflectionClass(ucfirst(CFRouter::$controller) . '_Controller');
                    // Start validation of the controller
                } catch (ReflectionException $e) {
                    //something went wrong
                    return null;
                }
            }

            if (isset($reflectionClass) && ($reflectionClass->isAbstract() OR ( IN_PRODUCTION AND $reflectionClass->getConstant('ALLOW_PRODUCTION') == FALSE))) {
                // Controller is not allowed to run in production
                return null;
            }
        }


        return $reflectionClass;
    }

    public static function getReflectionControllerMethodAndArguments(ReflectionClass $reflectionClass) {
        $method = null;
        $arguments = [];
        try {
            // Load the controller method
            $method = $reflectionClass->getMethod(CFRouter::$method);

            // Method exists
            if (CFRouter::$method[0] === '_') {
                return null;
            }

            if ($method->isProtected() or $method->isPrivate()) {
                // Do not attempt to invoke protected methods
                throw new ReflectionException('protected controller method');
            }

            // Default arguments
            $arguments = CFRouter::$arguments;
        } catch (ReflectionException $e) {
            // Use __call instead
            $method = $reflectionClass->getMethod('__call');

            // Use arguments in __call format
            $arguments = array(CFRouter::$method, CFRouter::$arguments);
        }

        return [$method, $arguments];
    }

    public function invokeController(CHTTP_Request $request) {
        CFBenchmark::start(SYSTEM_BENCHMARK . '_controller_setup');
        $reflectionClass = $this->getReflectionControllerClass();
        $reflectionMethod = null;
        $arguments = [];
        $response = null;
        if ($reflectionClass) {
            //class is found then we will try to find the method
            list($reflectionMethod, $arguments) = $this->getReflectionControllerMethodAndArguments($reflectionClass);
        }
        // Stop the controller setup benchmark
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_setup');

        // Start the controller execution benchmark
        CFBenchmark::start(SYSTEM_BENCHMARK . '_controller_execution');



        if ($reflectionMethod == null) {
            CF::show404();
        } else {
            // Execute the controller method
            $this->controller = $reflectionClass->newInstance();
            $response = $reflectionMethod->invokeArgs($this->controller, $arguments);
        }

        // Stop the controller execution benchmark
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_execution');

        return $response;
    }

    public function controller() {
        return $this->controller;
    }

    public function sendRequest($request) {

        $this->startOutputBuffering();


        $kernel = $this;
        register_shutdown_function(function() use ($kernel) {
            if (!$kernel->isHandled()) {
                $output = $kernel->cleanOutputBuffer();
                if (strlen($output) > 0) {
                    echo $output;
                }
            }
        });
        $output = '';
        $response = null;
        try {
            $response = $this->invokeController($request);
        } catch (Exception $e) {

            throw $e;
        } finally {

            $output = $this->cleanOutputBuffer();
        }
        if ($response instanceof CInterface_Responsable) {
            $response = $response->toResponse($request);
        }
        if ($response == null || is_bool($response)) {
            $response = $output;
        }

        $response = $this->toResponse($request, $response);


        return $response;
    }

    public function handle(CHTTP_Request $request) {
        CBootstrap::instance()->boot();

        $response = null;
        try {
            $this->setupRouter();
            $response = $this->sendRequest($request);
        } catch (Exception $e) {


            $this->reportException($e);

            $response = $this->renderException($request, $e);
        } catch (Throwable $e) {

            $this->reportException($e);

            $response = $this->renderException($request, $e);
        }

        CEvent::dispatch(new CHTTP_Event_RequestHandled($request, $response));
//        if($response->getStatusCode()!=200) {
//            $this->endOutputBuffering();
//        }

        $this->isHandled = true;


        return $response;
    }

    public function terminate($request, $response) {
        if (!$this->terminated) {
            $this->terminated = true;
        }
    }

    public function isHandled() {
        return $this->isHandled;
    }

    /**
     * Static version of prepareResponse.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  mixed  $response
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public static function toResponse($request, $response) {
        if ($response instanceof CInterface_Responsable) {
            $response = $response->toResponse($request);
        }

        if ($response instanceof PsrResponseInterface) {
            $response = (new HttpFoundationFactory)->createResponse($response);
        } elseif ($response instanceof CModel && $response->wasRecentlyCreated) {
            $response = new CHTTP_JsonResponse($response, 201);
        } elseif (!$response instanceof SymfonyResponse &&
                ($response instanceof CInterface_Arrayable ||
                $response instanceof CInterface_Jsonable ||
                $response instanceof ArrayObject ||
                $response instanceof JsonSerializable ||
                is_array($response))) {
            $response = new CHTTP_JsonResponse($response);
        } elseif (!$response instanceof SymfonyResponse) {
            $response = new CHTTP_Response($response, 200, ['Content-Type' => 'text/html']);
        }

        if ($response->getStatusCode() === CHTTP_Response::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($request);
    }

}

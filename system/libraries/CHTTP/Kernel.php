<?php

/**
 * Description of Kernel
 *
 * @author Hery
 */
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class CHTTP_Kernel {

    protected $isHandled = false;
    
    public function __construct() {
        
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Exception  $e
     * @return void
     */
    protected function reportException(Exception $e) {
        CException::exceptionHandler()->report($e);
    }

    /**
     * Render the exception to a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderException($request, Exception $e) {
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
        if ($reflectionClass) {
            //class is found then we will try to find the method
            list($reflectionMethod, $arguments) = $this->getReflectionControllerMethodAndArguments($reflectionClass);
        }
        // Stop the controller setup benchmark
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_setup');

        // Start the controller execution benchmark
        CFBenchmark::start(SYSTEM_BENCHMARK . '_controller_execution');

        
        
        if($reflectionMethod ==null) {
            CF::show404();
        }
        // Execute the controller method
        $response = $reflectionMethod->invokeArgs($reflectionClass->newInstance(), $arguments);


        // Stop the controller execution benchmark
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_execution');

        return $response;
    }

    public function sendRequest($request) {
        $outputBuffer = '';

        $outputBufferLevel = ob_get_level();

        ob_start(function($output) use (&$outputBuffer) {
            $outputBuffer = $output;
        });
        
        $kernel = $this;
        register_shutdown_function(function() use ($outputBufferLevel, $kernel) {
            if(!$kernel->isHandled()) {
                //process is terminated when invoke controller
                $output = '';
                if (ob_get_level() >= $outputBufferLevel) {
                    while (ob_get_level() > $outputBufferLevel) {
                        // Flush 
                        $output.= ob_get_clean();
                    }
                    // Store the output buffer
                    $output.= ob_get_clean();

                }
               
                echo $output;
            }
            
        });
        
        try {
            $response = $this->invokeController($request);
        } catch (Exception $e) {

            throw $e;
        } finally {

            if (ob_get_level() >= $outputBufferLevel) {
                while (ob_get_level() > $outputBufferLevel) {
                    // Flush 
                    ob_end_flush();
                }
                // Store the output buffer
                ob_end_clean();
            }
        }
        
        if($response instanceof CInterface_Responsable) {
            $response = $response->toResponse($request);
        }
        if ($response == null || is_bool($response)) {
            $response = $outputBuffer;
        }

        if (!($response instanceof SymfonyResponse)) {
            
            $response = CHTTP::createResponse($response);
        }
        

        return $response;
    }

    public function handle(CHTTP_Request $request) {
        try {
            $this->setupRouter();
            $response = $this->sendRequest($request);
        } catch (Exception $e) {
            $this->reportException($e);
            
            $response = $this->renderException($request, $e);
            
        } catch (Throwable $e) {
            $this->reportException($e = new FatalThrowableError($e));

            $response = $this->renderException($request, $e);
        }
        $this->isHandled = true;
        
        return $response;
    }

    public function terminate($request, $response) {
        
    }
    
    public function isHandled() {
        return $this->isHandled;
    }

}

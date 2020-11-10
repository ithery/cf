<?php

/**
 * Description of Kernel
 *
 * @author Hery
 */
class CHTTP_Kernel {

    public function __construct() {
        
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
                    $class = new ReflectionClass(ucfirst(CFRouter::$controller) . '_Controller');
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
        CFBenchmark::stop(SYSTEM_BENCHMARK . '_controller_start');
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


        // Execute the controller method
        $response = $reflectionMethod->invokeArgs($reflectionClass->newInstance(), $arguments);

        if (!$response instanceof CHTTP_Response) {
            $response = CHTTP::createResponse($response);
        }

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
        $response = $this->invokeController($request);
        if (ob_get_level() >= $outputBufferLevel) {
            while (ob_get_level() > $outputBufferLevel) {
                // Flush 
                ob_end_flush();
            }
            // Store the C output buffer
            ob_end_clean();
        }
        if ($response == null) {
            $response = $outputBuffer;
        }
        
        if (!($response instanceof CHTTP_Response)) {
            $response = CHTTP::createResponse($response);
        }
        
        return $response;
    }

    public function handle(CHTTP_Request $request) {
        $this->setupRouter();
        $response = $this->invokeController($request);
        return $response;
    }

    public function terminate($request, $response) {
        
    }

}

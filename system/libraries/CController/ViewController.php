<?php

class CController_ViewController extends CController {
    /**
     * Create a new controller instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Invoke the controller method.
     *
     * @param array $args
     *
     * @return \CHTTP_Response
     */
    public function __invoke(...$args) {
        list($view, $data, $status, $headers) = array_slice($args, -4);

        return CHTTP::responseFactory()->view($view, $data, $status, $headers);
    }
}

<?php

/**
 * Description of KernelRouting.
 *
 * @author Hery
 */
trait CHTTP_Concern_KernelRouting {
    /**
     * Send the given request through the middleware / router.
     *
     * @param \CHttp_Request $request
     *
     * @return \CHttp_Response
     */
    protected function sendRequestThroughRouter($request) {
        //$this->bootstrap();

        return (new CHTTP_Pipeline())
            ->send($request)
            ->through(CHTTP::shouldSkipMiddleware() ? [] : CMiddleware::middleware())
            ->then($this->dispatchToRouter());
    }

    /**
     * Get the route dispatcher callback.
     *
     * @return \Closure
     */
    protected function dispatchToRouter() {
        return function ($request) {
            return CRouting::router()->dispatch($request);
        };
    }
}

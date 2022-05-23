<?php

trait CApi_Trait_HasGroupPropertyTrait {
    protected $group;

    /**
     * Report the exception to the exception handler.
     *
     * @param \Exception $e
     *
     * @return void
     */
    protected function reportException($e) {
        c::api($this->group)->exceptionHandler()->report($e);
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
        return c::api($this->group)->exceptionHandler()->render($request, $e);
    }

    public function manager() {
        return c::api($this->group);
    }
}

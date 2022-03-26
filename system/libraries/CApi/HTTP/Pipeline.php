<?php

class CApi_HTTP_Pipeline extends CHTTP_Pipeline {
    use CApi_Trait_HasGroupPropertyTrait;

    public function __construct($group) {
        parent::__construct();
        $this->group = $group;
    }

    /**
     * Handle the given exception.
     *
     * @param mixed      $passable
     * @param \Throwable $e
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    protected function handleException($passable, $e) {
        if (!$passable instanceof CHTTP_Request) {
            throw $e;
        }

        $handler = $this->manager()->exceptionHandler();

        $handler->report($e);

        $response = $handler->render($passable, $e);

        if (is_object($response) && method_exists($response, 'withException')) {
            /** @var CHTTP_Response $response */
            $response->withException($e);
        }

        return $response;
    }
}

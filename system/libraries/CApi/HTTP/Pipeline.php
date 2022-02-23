<?php

class CApi_HTTP_Pipeline extends CHTTP_Pipeline {
    protected $group;

    /**
     * @param string $group
     *
     * @return $this
     */
    public function setGroup($group) {
        $this->group = $group;

        return $this;
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

        $handler = CApi_Manager::instance($this->group)->exceptionHandler();

        $handler->report($e);

        $response = $handler->render($passable, $e);

        if (is_object($response) && method_exists($response, 'withException')) {
            /** @var CHTTP_Response $response */
            $response->withException($e);
        }

        return $response;
    }
}

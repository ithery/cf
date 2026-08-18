<?php


class CApi_Event_AfterDispatch {
    /**
     * The method response instance.
     *
     * @var Symfony\Component\HttpFoundation\Response
     */
    public $response;

    /**
     * Create a new event instance.
     *
     * @param \Symfony\Component\HttpFoundation\Response $response
     *
     * @return void
     */
    public function __construct($response) {
        $this->response = $response;
    }
}

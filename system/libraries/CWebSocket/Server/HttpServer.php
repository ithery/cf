<?php
use Ratchet\Http\HttpServerInterface;
use Ratchet\Http\HttpServer as BaseHttpServer;

class CWebSocket_Server_HttpServer extends BaseHttpServer {
    /**
     * Create a new server instance.
     *
     * @param \Ratchet\Http\HttpServerInterface $component
     * @param int                               $maxRequestSize
     *
     * @return void
     */
    public function __construct(HttpServerInterface $component, $maxRequestSize = 4096) {
        parent::__construct($component);

        $this->_reqParser->maxSize = $maxRequestSize;
    }
}

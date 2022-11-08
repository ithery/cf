<?php

use GuzzleHttp\Utils;

/**
 * @mixin \CHTTP_Client_Factory
 */
class CHTTP_Client_Pool {
    /**
     * The factory instance.
     *
     * @var \CHTTP_Client
     */
    protected $factory;

    /**
     * The handler function for the Guzzle client.
     *
     * @var callable
     */
    protected $handler;

    /**
     * The pool of requests.
     *
     * @var array
     */
    protected $pool = [];

    /**
     * Create a new requests pool.
     *
     * @param null|\CHTTP_Client $factory
     *
     * @return void
     */
    public function __construct(CHTTP_Client $factory = null) {
        $this->factory = $factory ?: new CHTTP_Client();

        if (method_exists(Utils::class, 'chooseHandler')) {
            $this->handler = Utils::chooseHandler();
        } else {
            $this->handler = \GuzzleHttp\choose_handler();
        }
    }

    /**
     * Add a request to the pool with a key.
     *
     * @param string $key
     *
     * @return \CHTTP_Client_PendingRequest
     */
    public function as($key) {
        return $this->pool[$key] = $this->asyncRequest();
    }

    /**
     * Retrieve a new async pending request.
     *
     * @return \CHTTP_Client_PendingRequest
     */
    protected function asyncRequest() {
        return $this->factory->setHandler($this->handler)->async();
    }

    /**
     * Retrieve the requests in the pool.
     *
     * @return array
     */
    public function getRequests() {
        return $this->pool;
    }

    /**
     * Add a request to the pool with a numeric index.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return \CHTTP_Client_PendingRequest
     */
    public function __call($method, $parameters) {
        return $this->pool[] = $this->asyncRequest()->$method(...$parameters);
    }
}

<?php

class CBot_MiddlewareManager {
    /**
     * @var CBot_Contract_Middleware_ReceivedInterface[]
     */
    protected $received = [];

    /**
     * @var CBot_Contract_Middleware_CapturedInterface[]
     */
    protected $captured = [];

    /**
     * @var CBot_Contract_Middleware_MatchingInterface[]
     */
    protected $matching = [];

    /**
     * @var CBot_Contract_Middleware_HeardInterface[]
     */
    protected $heard = [];

    /**
     * @var CBot_Contract_Middleware_SendingInterface[]
     */
    protected $sending = [];

    /**
     * @var CBot_Bot
     */
    protected $bot;

    public function __construct(CBot_Bot $bot) {
        $this->bot = $bot;
    }

    /**
     * @param CBot_Contract_Middleware_ReceivedInterface[] ...$middleware
     *
     * @return CBot_Contract_Middleware_ReceivedInterface[]|$this
     */
    public function received(CBot_Contract_Middleware_ReceivedInterface ...$middleware) {
        if (empty($middleware)) {
            return $this->received;
        }
        $this->received = array_merge($this->received, $middleware);

        return $this;
    }

    /**
     * @param CBot_Contract_Middleware_CapturedInterface[] ...$middleware
     *
     * @return CBot_Contract_Middleware_CapturedInterface[]|$this
     */
    public function captured(CBot_Contract_Middleware_CapturedInterface ...$middleware) {
        if (empty($middleware)) {
            return $this->captured;
        }
        $this->captured = array_merge($this->captured, $middleware);

        return $this;
    }

    /**
     * @param CBot_Contract_Middleware_MatchingInterface[] ...$middleware
     *
     * @return CBot_Contract_Middleware_MatchingInterface[]|$this
     */
    public function matching(CBot_Contract_Middleware_MatchingInterface ...$middleware) {
        if (empty($middleware)) {
            return $this->matching;
        }
        $this->matching = array_merge($this->matching, $middleware);

        return $this;
    }

    /**
     * @param CBot_Contract_Middleware_HeardInterface[] $middleware
     *
     * @return CBot_Contract_Middleware_HeardInterface[]|$this
     */
    public function heard(CBot_Contract_Middleware_HeardInterface ...$middleware) {
        if (empty($middleware)) {
            return $this->heard;
        }
        $this->heard = array_merge($this->heard, $middleware);

        return $this;
    }

    /**
     * @param CBot_Contract_Middleware_SendingInterface[] $middleware
     *
     * @return CBot_Contract_Middleware_SendingInterface[]|$this
     */
    public function sending(CBot_Contract_Middleware_SendingInterface ...$middleware) {
        if (empty($middleware)) {
            return $this->sending;
        }
        $this->sending = array_merge($this->sending, $middleware);

        return $this;
    }

    /**
     * @param string                              $method
     * @param mixed                               $payload
     * @param CBot_Contract_MiddlewareInterface[] $additionalMiddleware
     * @param null|Closure                        $destination
     *
     * @return mixed
     */
    public function applyMiddleware($method, $payload, array $additionalMiddleware = [], Closure $destination = null) {
        $destination = is_null($destination) ? function ($payload) {
            return $payload;
        }
        : $destination;

        $middleware = $this->$method + $additionalMiddleware;

        return (new CBase_Pipeline())
            ->via($method)
            ->send($payload)
            ->with($this->bot)
            ->through($middleware)
            ->then($destination);
    }
}

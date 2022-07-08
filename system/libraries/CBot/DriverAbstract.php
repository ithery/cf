<?php

abstract class CBot_DriverAbstract implements CBot_Contract_DriverInterface {
    /**
     * @var CCollection|\Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $payload;

    /**
     * @var CCollection
     */
    protected $event;

    /**
     * @var CCollection
     */
    protected $config;

    /**
     * @var string
     */
    protected $content;

    /**
     * Driver constructor.
     *
     * @param CHTTP_Request $request
     * @param array         $config
     */
    final public function __construct(CHTTP_Request $request, array $config) {
        $this->config = c::collect($config);
        $this->content = $request->getContent();
        $this->buildPayload($request);
    }

    /**
     * Return the driver name.
     *
     * @return string
     */
    abstract public static function getName();

    /**
     * Return the driver configuration.
     *
     * @return Collection
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Return the raw request content.
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return void
     */
    public function types(CBot_Message_Incoming_IncomingMessage $matchingMessage) {
        // Do nothing
    }

    /**
     * Send a typing indicator and wait for the given amount of seconds.
     *
     * @param CBot_Message_Incoming_IncomingMessage $matchingMessage
     * @param float                                 $seconds
     *
     * @return mixed
     */
    public function typesAndWaits(CBot_Message_Incoming_IncomingMessage $matchingMessage, $seconds) {
        $this->types($matchingMessage);
        usleep($seconds * 1000000);
    }

    /**
     * @return bool
     */
    public function hasMatchingEvent() {
        return false;
    }

    /**
     * @param CHTTP_Request $request
     *
     * @return void
     */
    abstract public function buildPayload(CHTTP_Request $request);

    /**
     * Low-level method to perform driver specific API requests.
     *
     * @param string                                 $endpoint
     * @param array                                  $parameters
     * @param \CBot_Message_Incoming_IncomingMessage $matchingMessage
     *
     * @return void
     */
    abstract public function sendRequest($endpoint, array $parameters, CBot_Message_Incoming_IncomingMessage $matchingMessage);

    /**
     * Tells if the stored conversation callbacks are serialized.
     *
     * @return bool
     */
    public function serializesCallbacks() {
        return true;
    }
}

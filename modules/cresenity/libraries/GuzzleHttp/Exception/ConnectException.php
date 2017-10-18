<?php

/**
 * Exception thrown when a connection cannot be established.
 *
 * Note that no response is present for a ConnectException
 */
class GuzzleHttp_Exception_ConnectException extends GuzzleHttp_Exception_RequestException
{
    public function __construct(
        $message,
        Psr_Http_Message_RequestInterface $request,
        \Exception $previous = null,
        array $handlerContext = []
    ) {
        parent::__construct($message, $request, null, $previous, $handlerContext);
    }

    /**
     * @return null
     */
    public function getResponse()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function hasResponse()
    {
        return false;
    }
}

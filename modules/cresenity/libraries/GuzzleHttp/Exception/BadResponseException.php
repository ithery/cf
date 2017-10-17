<?php

/**
 * Exception when an HTTP error occurs (4xx or 5xx error)
 */
class GuzzleHttp_Exception_BadResponseException extends GuzzleHttp_Exception_RequestException
{
    public function __construct(
        $message,
        Psr_Http_Message_RequestInterface $request,
        Psr_Http_Message_ResponseInterface $response = null,
        \Exception $previous = null,
        array $handlerContext = []
    ) {
        if (null === $response) {
            @trigger_error(
                'Instantiating the ' . __CLASS__ . ' class without a Response is deprecated since version 6.3 and will be removed in 7.0.',
                E_USER_DEPRECATED
            );
        }
        parent::__construct($message, $request, $response, $previous, $handlerContext);
    }
}

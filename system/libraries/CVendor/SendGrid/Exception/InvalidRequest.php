<?php

/**
 * Class InvalidHttpRequest
 *
 * Thrown when invalid payload was constructed, which could not reach SendGrid server.
 *
 * @package SendGrid\Exceptions
 */
class CVendor_SendGrid_Exception_InvalidRequest extends \Exception {
    public function __construct(
        $message = '',
        $code = 0,
        Throwable $previous = null
    ) {
        $message = 'Could not send request to server. '
                . 'CURL error ' . $code . ': ' . $message;
        parent::__construct($message, $code, $previous);
    }
}

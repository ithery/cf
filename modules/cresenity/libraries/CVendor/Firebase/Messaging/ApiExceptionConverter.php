<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;


use Psr\Http\Message\ResponseInterface;


/**
 * @internal
 */
class CVendor_Firebase_Messaging_ApiExceptionConverter
{
    /** @var ErrorResponseParser */
    private $responseParser;

    /**
     * @internal
     */
    public function __construct()
    {
        $this->responseParser = new CVendor_Firebase_Http_ErrorResponseParser();
    }

    /**
     * @return MessagingException
     */
    public function convertException( $exception)
    {
        if ($exception instanceof RequestException) {
            return $this->convertGuzzleRequestException($exception);
        }

        return new CVendor_Firebase_Messaging_Exception_MessagingErrorException($exception->getMessage(), $exception->getCode(), $exception);
    }

    public function convertResponse(ResponseInterface $response,  $previous = null)
    {
        $code = $response->getStatusCode();

        if ($code < 400) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('Cannot convert a non-failed response to an exception');
        }

        $errors = $this->responseParser->getErrorsFromResponse($response);
        $message = $this->responseParser->getErrorReasonFromResponse($response);

        switch ($code) {
            case 400:
                $convertedError = new CVendor_Firebase_Messaging_Exception_InvalidMessageException($message, $code, $previous);
                break;
            case 401:
            case 403:
                $convertedError = new CVendor_Firebase_Messaging_Exception_AuthenticationErrorException($message, $code, $previous);
                break;
            case 404:
                $convertedError = new CVendor_Firebase_Messaging_Exception_NotFoundException($message, $code, $previous);
                break;
            case 500:
                $convertedError = new CVendor_Firebase_Messaging_Exception_ServerErrorException($message, $code, $previous);
                break;
            case 503:
                $convertedError = new CVendor_Firebase_Messaging_Exception_ServerUnavailableException($message, $code, $previous);
                break;
            default:
                $convertedError = new CVendor_Firebase_Messaging_Exception_MessagingErrorException($message, $code, $previous);
                break;
        }

        return $convertedError
            ->withErrors($errors)
            ->withResponse($response);
    }

    private function convertGuzzleRequestException(RequestException $e)
    {
        if ($e instanceof ConnectException) {
            return new CVendor_Firebase_Messaging_Exception_ApiConnectionFailedException($e->getMessage(), $e->getCode(), $e);
        }

        if ($response = $e->getResponse()) {
            return $this->convertResponse($response);
        }

        return new CVendor_Firebase_Messaging_Exception_MessagingErrorException($e->getMessage(), $e->getCode(), $e);
    }
}
<?php

use Beste\Clock\SystemClock;
use Psr\Clock\ClockInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\NetworkExceptionInterface;

/**
 * @internal
 */
class CVendor_Firebase_Messaging_ApiExceptionConverter {
    /**
     * @var CVendor_Firebase_Http_ErrorResponseParser
     */
    private $responseParser;

    /**
     * @var ClockInterface
     */
    private $clock;

    /**
     * @internal
     */
    public function __construct(ClockInterface $clock = null) {
        $this->responseParser = new CVendor_Firebase_Http_ErrorResponseParser();
        $this->clock = $clock ?: SystemClock::create();
    }

    /**
     * @param mixed $exception
     *
     * @return CVendor_Firebase_Messaging_ExceptionInterface
     */
    public function convertException($exception) {
        if ($exception instanceof RequestException) {
            return $this->convertGuzzleRequestException($exception);
        }
        if ($exception instanceof NetworkExceptionInterface) {
            return new CVendor_Firebase_Messaging_Exception_ApiConnectionFailedException('Unable to connect to the API: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }

        return new CVendor_Firebase_Messaging_Exception_MessagingErrorException($exception->getMessage(), $exception->getCode(), $exception);
    }

    public function convertResponse(ResponseInterface $response, $previous = null) {
        $code = $response->getStatusCode();
        $request = null;
        if ($previous instanceof RequestException) {
            $request = $previous->getRequest();
        }
        if ($code < 400) {
            throw new CVendor_Firebase_Exception_InvalidArgumentException('Cannot convert a non-failed response to an exception');
        }

        $errors = $this->responseParser->getErrorsFromResponse($response, $request);
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
            case 429:
                $convertedError = new CVendor_Firebase_Messaging_Exception_QuotaExceededException($message);
                $retryAfter = $this->getRetryAfter($response);

                if ($retryAfter !== null) {
                    $convertedError = $convertedError->withRetryAfter($retryAfter);
                }

                break;
            case 500:
                $convertedError = new CVendor_Firebase_Messaging_Exception_ServerErrorException($message, $code, $previous);

                break;
            case 503:
                $convertedError = new CVendor_Firebase_Messaging_Exception_ServerUnavailableException($message, $code, $previous);
                $retryAfter = $this->getRetryAfter($response);

                if ($retryAfter !== null) {
                    $convertedError = $convertedError->withRetryAfter($retryAfter);
                }

                break;
            default:
                $convertedError = new CVendor_Firebase_Messaging_Exception_MessagingErrorException($message, $code, $previous);

                break;
        }

        return $convertedError->withErrors($errors);
    }

    private function convertGuzzleRequestException(RequestException $e) {
        if ($e instanceof ConnectException) {
            return new CVendor_Firebase_Messaging_Exception_ApiConnectionFailedException($e->getMessage(), $e->getCode(), $e);
        }

        if ($response = $e->getResponse()) {
            return $this->convertResponse($response, $e);
        }

        return new CVendor_Firebase_Messaging_Exception_MessagingErrorException($e->getMessage(), $e->getCode(), $e);
    }

    /**
     * @param ResponseInterface $response
     *
     * @return null|DateTimeImmutable
     */
    private function getRetryAfter(ResponseInterface $response) {
        $retryAfter = isset($response->getHeader('Retry-After')[0]) ? $response->getHeader('Retry-After')[0] : null;

        if (!$retryAfter) {
            return null;
        }

        if (\is_numeric($retryAfter)) {
            return $this->clock->now()->modify("+{$retryAfter} seconds");
        }

        try {
            return new DateTimeImmutable($retryAfter);
        } catch (Throwable $e) {
            // We can't afford to throw exceptions in an exception handler :)
            // Here, if the Retry-After header doesn't have a numeric value
            // or a date that can be handled by DateTimeImmutable, we just
            // throw it away, sorry not sorry ¯\_(ツ)_/¯
            return null;
        }
    }
}

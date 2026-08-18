<?php

use Http\Promise\Promise;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class CVendor_MailerSend_Helpers_HttpErrorHelper implements Plugin {
    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise {
        $promise = $next($request);

        return $promise->then(function (ResponseInterface $response) use ($request) {
            $code = $response->getStatusCode();

            if ($code >= 200 && $code < 400) {
                return $response;
            }

            if ($code === 422) {
                throw new CVendor_MailerSend_Exceptions_MailerSendValidationException($request, $response);
            }

            if ($code === 429) {
                throw new CVendor_MailerSend_Exceptions_MailerSendRateLimitException($request, $response);
            }

            throw new CVendor_MailerSend_Exceptions_MailerSendHttpException($request, $response);
        });
    }
}

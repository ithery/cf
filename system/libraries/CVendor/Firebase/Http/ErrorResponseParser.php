<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;

final class CVendor_Firebase_Http_ErrorResponseParser {
    public function getErrorReasonFromResponse(ResponseInterface $response) {
        $responseBody = (string) $response->getBody();

        try {
            $data = CHelper::json()->decode($responseBody, true);
        } catch (InvalidArgumentException $e) {
            return $responseBody;
        }

        if (\is_string(isset($data['error']['message']) ? $data['error']['message'] : null)) {
            return $data['error']['message'];
        }

        if (\is_string(isset($data['error']) ? $data['error'] : null)) {
            return $data['error'];
        }

        return $responseBody;
    }

    public function getErrorsFromResponse(ResponseInterface $response, RequestInterface $request = null) {
        $responseBody = null;
        if ($response) {
            $responseBody = json_decode((string) $response->getBody(), true);
            if ($responseBody == null) {
                $messageError = 'response is not json:' . (string) $response->getBody();
                if ($request) {
                    $messageError.= 'on url:' . $request->getUri();
                }
                throw new Exception($messageError);
            }
        }

        return $responseBody;
    }
}

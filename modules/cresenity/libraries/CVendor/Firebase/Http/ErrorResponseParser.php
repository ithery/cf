<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use Psr\Http\Message\ResponseInterface;

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

    public function getErrorsFromResponse(ResponseInterface $response) {
        try {
            return CHelper::json()->decode((string) $response->getBody(), true);
        } catch (\InvalidArgumentException $e) {
            return [];
        }
    }

}

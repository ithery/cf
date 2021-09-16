<?php

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class CHTTP_ResponseCache_Serializer_DefaultSerializer implements CHTTP_ResponseCache_Serializer_SerializerInterface {
    const RESPONSE_TYPE_NORMAL = 'normal';

    const RESPONSE_TYPE_FILE = 'file';

    public function serialize(Response $response) {
        return serialize($this->getResponseData($response));
    }

    public function unserialize($serializedResponse) {
        $responseProperties = unserialize($serializedResponse);

        if (!$this->containsValidResponseProperties($responseProperties)) {
            throw CHTTP_ResponseCache_Exception_CouldNotUnserializeException::serializedResponse($serializedResponse);
        }

        $response = $this->buildResponse($responseProperties);

        $response->headers = $responseProperties['headers'];

        return $response;
    }

    /**
     * @param Response $response
     *
     * @return array
     */
    protected function getResponseData(Response $response) {
        $statusCode = $response->getStatusCode();
        $headers = $response->headers;

        if ($response instanceof BinaryFileResponse) {
            $content = $response->getFile()->getPathname();
            $type = static::RESPONSE_TYPE_FILE;

            return compact('statusCode', 'headers', 'content', 'type');
        }

        $content = $response->getContent();
        $type = static::RESPONSE_TYPE_NORMAL;

        return compact('statusCode', 'headers', 'content', 'type');
    }

    /**
     * @param mixed $properties
     *
     * @return bool
     */
    protected function containsValidResponseProperties($properties) {
        if (!is_array($properties)) {
            return false;
        }

        if (!isset($properties['content'], $properties['statusCode'])) {
            return false;
        }

        return true;
    }

    protected function buildResponse(array $responseProperties) {
        $type = isset($responseProperties['type']) && $responseProperties['type'] != null ? $responseProperties['type'] : static::RESPONSE_TYPE_NORMAL;

        if ($type === static::RESPONSE_TYPE_FILE) {
            return new BinaryFileResponse(
                $responseProperties['content'],
                $responseProperties['statusCode']
            );
        }

        return new CHTTP_Response($responseProperties['content'], $responseProperties['statusCode']);
    }
}

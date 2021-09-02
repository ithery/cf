<?php
use Symfony\Component\HttpFoundation\Response;

interface CHTTP_ResponseCache_Serializer_SerializerInterface {
    public function serialize(Response $response);

    public function unserialize($serializedResponse);
}

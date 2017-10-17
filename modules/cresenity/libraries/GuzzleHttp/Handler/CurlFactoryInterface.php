<?php


interface GuzzleHttp_Handler_CurlFactoryInterface
{
    /**
     * Creates a cURL handle resource.
     *
     * @param Psr_Http_Message_RequestInterface $request Request
     * @param array                             $options Transfer options
     *
     * @return EasyHandle
     * @throws RuntimeException when an option cannot be applied
     */
    public function create(Psr_Http_Message_RequestInterface $request, array $options);

    /**
     * Release an easy handle, allowing it to be reused or closed.
     *
     * This function must call unset on the easy handle's "handle" property.
     *
     * @param EasyHandle $easy
     */
    public function release(GuzzleHttp_Handler_EasyHandle $easy);
}

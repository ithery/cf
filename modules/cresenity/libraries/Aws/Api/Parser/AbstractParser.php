<?php

/**
 * @internal
 */
abstract class Aws_Api_Parser_AbstractParser {

    /** @var \Aws\Api\Service Representation of the service API */
    protected $api;

    /**
     * @param Service $api Service description.
     */
    public function __construct(Aws_Api_Service $api) {
        $this->api = $api;
    }

    /**
     * @param CommandInterface  $command  Command that was executed.
     * @param ResponseInterface $response Response that was received.
     *
     * @return ResultInterface
     */
    abstract public function __invoke(
    Aws_CommandInterface $command, Psr_Http_Message_ResponseInterface $response
    );
}

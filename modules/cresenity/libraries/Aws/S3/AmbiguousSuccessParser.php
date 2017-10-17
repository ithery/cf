<?php

/**
 * Converts errors returned with a status code of 200 to a retryable error type.
 *
 * @internal
 */
class Aws_S3_AmbiguousSuccessParser extends Aws_Api_Parser_AbstractParser {

    private static $ambiguousSuccesses = [
        'UploadPartCopy' => true,
        'CopyObject' => true,
        'CompleteMultipartUpload' => true,
    ];

    /** @var callable */
    private $parser;

    /** @var callable */
    private $errorParser;

    /** @var string */
    private $exceptionClass;

    public function __construct(
    callable $parser, callable $errorParser, $exceptionClass = Aws_Exception_AwsException::class
    ) {
        $this->parser = $parser;
        $this->errorParser = $errorParser;
        $this->exceptionClass = $exceptionClass;
    }

    public function __invoke(
    Aws_CommandInterface $command, Psr_Http_Message_ResponseInterface $response
    ) {
        if (200 === $response->getStatusCode() && isset(self::$ambiguousSuccesses[$command->getName()])
        ) {
            $errorParser = $this->errorParser;
            $parsed = $errorParser($response);
            if (isset($parsed['code']) && isset($parsed['message'])) {
                throw new $this->exceptionClass(
                $parsed['message'], $command, ['connection_error' => true]
                );
            }
        }

        $fn = $this->parser;
        return $fn($command, $response);
    }

}

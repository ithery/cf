<?php

/**
 * Converts malformed responses to a retryable error type.
 *
 * @internal
 */
class Aws_S3_RetryableMalformedResponseParser extends \Aws_Api_Parser_AbstractParser {

    /** @var callable */
    private $parser;

    /** @var string */
    private $exceptionClass;

    public function __construct(
    callable $parser, $exceptionClass = AwsException::class
    ) {
        $this->parser = $parser;
        $this->exceptionClass = $exceptionClass;
    }

    public function __invoke(
    Aws_CommandInterface $command, Psr_Http_Message_ResponseInterface $response
    ) {
        $fn = $this->parser;

        try {
            return $fn($command, $response);
        } catch (Aws_Api_Parser_Exception_ParserException $e) {
            throw new $this->exceptionClass(
            "Error parsing response for {$command->getName()}:"
            . " AWS parsing error: {$e->getMessage()}", $command, ['connection_error' => true, 'exception' => $e], $e
            );
        }
    }

}

<?php

/**
 * @internal Decorates a parser for the S3 service to correctly handle the
 *           GetBucketLocation operation.
 */
class Aws_S3_GetBucketLocationParser extends Aws_Api_Parser_AbstractParser {

    /** @var callable */
    private $parser;

    /**
     * @param callable $parser Parser to wrap.
     */
    public function __construct(callable $parser) {
        $this->parser = $parser;
    }

    public function __invoke(
    Aws_CommandInterface $command, Psr_Http_Message_ResponseInterface $response
    ) {
        $fn = $this->parser;
        $result = $fn($command, $response);

        if ($command->getName() === 'GetBucketLocation') {
            $location = 'us-east-1';
            if (preg_match('/>(.+?)<\/LocationConstraint>/', $response->getBody(), $matches)) {
                $location = $matches[1] === 'EU' ? 'eu-west-1' : $matches[1];
            }
            $result['LocationConstraint'] = $location;
        }

        return $result;
    }

}

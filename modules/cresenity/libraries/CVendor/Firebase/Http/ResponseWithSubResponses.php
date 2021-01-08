<?php

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Riverline\MultiPartParser\Converters\PSR7;
use Riverline\MultiPartParser\StreamedPart;

final class CVendor_Firebase_Http_ResponseWithSubResponses implements CVendor_Firebase_Http_HasSubResponsesInterface, ResponseInterface {
    use CVendor_Firebase_Trait_WrappedPsr7ResponseTrait;

    /** @var Responses */
    private $subResponses;

    public function __construct(ResponseInterface $response) {
        $this->wrappedResponse = $response;
        $this->subResponses = $this->getSubResponsesFromResponse($response);
    }

    public function subResponses() {
        return $this->subResponses;
    }

    private function getSubResponsesFromResponse(ResponseInterface $response) {
        try {
            $parser = PSR7::convert($response);
        } catch (Throwable $e) {
            return new CVendor_Firebase_Http_Responses();
        }

        if (!$parser->isMultiPart()) {
            return new CVendor_Firebase_Http_Responses();
        }

        $subResponses = [];

        foreach ($parser->getParts() as $part) {
            $partHeaders = $part->getHeaders();

            $realPartStream = \fopen('php://temp', 'rwb');
            if (!$realPartStream) {
                continue;
            }

            \fwrite($realPartStream, $part->getBody());
            \rewind($realPartStream);
            $realPart = new StreamedPart($realPartStream);

            $headers = $realPart->getHeaders();
            $headerKeys = \array_keys($headers);
            // The first header is not a header, it's the start line of a HTTP response
            $startLine = (string) \array_shift($headerKeys);
            \array_shift($headers);

            if (\preg_match('@^http/(?P<version>[\S]+)\s(?P<status>\d{3})\s(?P<reason>.+)$@i', $startLine, $startLineMatches) !== 1) {
                throw new CVendor_Firebase_Exception_InvalidArgumentException('At least one sub response does not contain a start line');
            }

            $subResponse = new Response(
                (int) $startLineMatches['status'],
                $headers,
                $realPart->getBody(),
                $startLineMatches['version'],
                $startLineMatches['reason']
            );

            foreach ($partHeaders as $name => $value) {
                $subResponse = $subResponse->withAddedHeader($name, $value);
            }

            $subResponses[] = $subResponse;
        }

        return new CVendor_Firebase_Http_Responses(...$subResponses);
    }
}

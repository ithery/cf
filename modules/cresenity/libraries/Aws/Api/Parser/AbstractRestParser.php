<?php

/**
 * @internal
 */
abstract class Aws_Api_Parser_AbstractRestParser extends Aws_Api_Parser_AbstractParser {

    use Aws_Api_Parser_PayloadParserTrait;

    /**
     * Parses a payload from a response.
     *
     * @param ResponseInterface $response Response to parse.
     * @param StructureShape    $member   Member to parse
     * @param array             $result   Result value
     *
     * @return mixed
     */
    abstract protected function payload(
    Psr_Http_Message_ResponseInterface $response, Aws_Api_StructureShape $member, array &$result
    );

    public function __invoke(
    Aws_CommandInterface $command, Psr_Http_Message_ResponseInterface $response
    ) {
        $output = $this->api->getOperation($command->getName())->getOutput();
        $result = [];

        if ($payload = $output['payload']) {
            $this->extractPayload($payload, $output, $response, $result);
        }

        foreach ($output->getMembers() as $name => $member) {
            switch ($member['location']) {
                case 'header':
                    $this->extractHeader($name, $member, $response, $result);
                    break;
                case 'headers':
                    $this->extractHeaders($name, $member, $response, $result);
                    break;
                case 'statusCode':
                    $this->extractStatus($name, $response, $result);
                    break;
            }
        }

        if (!$payload && $response->getBody()->getSize() > 0 && count($output->getMembers()) > 0
        ) {
            // if no payload was found, then parse the contents of the body
            $this->payload($response, $output, $result);
        }

        return new Aws_Result($result);
    }

    private function extractPayload(
    $payload, Aws_Api_StructureShape $output, Psr_Http_Message_ResponseInterface $response, array &$result
    ) {
        $member = $output->getMember($payload);

        if ($member instanceof Aws_Api_StructureShape) {
            // Structure members parse top-level data into a specific key.
            $result[$payload] = [];
            $this->payload($response, $member, $result[$payload]);
        } else {
            // Streaming data is just the stream from the response body.
            $result[$payload] = $response->getBody();
        }
    }

    /**
     * Extract a single header from the response into the result.
     */
    private function extractHeader(
    $name, Aws_Api_Shape $shape, Psr_Http_Message_ResponseInterface $response, &$result
    ) {
        $value = $response->getHeaderLine($shape['locationName'] ? : $name);

        switch ($shape->getType()) {
            case 'float':
            case 'double':
                $value = (float) $value;
                break;
            case 'long':
                $value = (int) $value;
                break;
            case 'boolean':
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                break;
            case 'blob':
                $value = base64_decode($value);
                break;
            case 'timestamp':
                try {
                    $value = new Aws_Api_DateTimeResult($value);
                    break;
                } catch (\Exception $e) {
                    // If the value cannot be parsed, then do not add it to the
                    // output structure.
                    return;
                }
            case 'string':
                if ($shape['jsonvalue']) {
                    $value = $this->parseJson(base64_decode($value));
                }
                break;
        }

        $result[$name] = $value;
    }

    /**
     * Extract a map of headers with an optional prefix from the response.
     */
    private function extractHeaders(
    $name, Aws_Api_Shape $shape, Psr_Http_Message_ResponseInterface $response, &$result
    ) {
        // Check if the headers are prefixed by a location name
        $result[$name] = [];
        $prefix = $shape['locationName'];
        $prefixLen = strlen($prefix);

        foreach ($response->getHeaders() as $k => $values) {
            if (!$prefixLen) {
                $result[$name][$k] = implode(', ', $values);
            } elseif (stripos($k, $prefix) === 0) {
                $result[$name][substr($k, $prefixLen)] = implode(', ', $values);
            }
        }
    }

    /**
     * Places the status code of the response into the result array.
     */
    private function extractStatus(
    $name, Psr_Http_Message_ResponseInterface $response, array &$result
    ) {
        $result[$name] = (int) $response->getStatusCode();
    }

}

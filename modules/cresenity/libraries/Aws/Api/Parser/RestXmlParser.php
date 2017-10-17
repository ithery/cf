<?php

/**
 * @internal Implements REST-XML parsing (e.g., S3, CloudFront, etc...)
 */
class Aws_Api_Parser_RestXmlParser extends Aws_Api_Parser_AbstractRestParser {

    use Aws_Api_Parser_PayloadParserTrait;

    /** @var XmlParser */
    private $parser;

    /**
     * @param Service   $api    Service description
     * @param XmlParser $parser XML body parser
     */
    public function __construct(Aws_Api_Service $api, Aws_Api_Parser_XmlParser $parser = null) {
        parent::__construct($api);
        $this->parser = $parser ? : new Aws_Api_Parser_XmlParser();
    }

    protected function payload(
    \Psr_Http_Message_ResponseInterface $response, Aws_Api_StructureShape $member, array &$result
    ) {
        $xml = $this->parseXml($response->getBody());
        $result += $this->parser->parse($member, $xml);
    }

}

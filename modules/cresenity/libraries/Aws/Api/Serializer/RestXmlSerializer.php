<?php

/**
 * @internal
 */
class Aws_Api_Serializer_RestXmlSerializer extends Aws_Api_Serializer_RestSerializer
{
    /** @var XmlBody */
    private $xmlBody;

    /**
     * @param Service $api      Service API description
     * @param string  $endpoint Endpoint to connect to
     * @param XmlBody $xmlBody  Optional XML formatter to use
     */
    public function __construct(
        Aws_Api_Service $api,
        $endpoint,
        Aws_Api_Serializer_XmlBody $xmlBody = null
    ) {
        parent::__construct($api, $endpoint);
        $this->xmlBody = $xmlBody ?: new Aws_Api_Serializer_XmlBody($api);
    }

    protected function payload(Aws_Api_StructureShape $member, array $value, array &$opts)
    {
        $opts['headers']['Content-Type'] = 'application/xml';
        $opts['body'] = (string) $this->xmlBody->build($member, $value);
    }
}

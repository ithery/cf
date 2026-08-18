<?php

class CReport_Builder_Element_PdfRaw extends CReport_Builder_ElementAbstract {
    protected $content;

    public function __construct() {
        parent::__construct();
        $this->content = null;
    }
    public function setContent($content) {
        $this->content = $content;
        return $this;
    }

    public function getContent($content) {
        return $this->content;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();
        $element->setContent((string) $xml);
        return $element;
    }

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<pdfraw';
        $openTag .= '>';

        $body = c::e($this->content);
        $closeTag = '</pdfraw>';
        $tag = $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;

        return $tag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $processor->raw($this->content);
    }
}

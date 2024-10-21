<?php

class CReport_Builder_Element_Frame extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasReportElementTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'reportElement') {
                $element->setReportElementPropertyFromXml($xmlElement);
            }
            $element->addChildrenFromXml($xmlElement);
        }

        return $element;
    }

    public function toJrXml() {
        $openTag = '<frame>';

        $reportElement = $this->getChildrenJrXml();
        $body = $reportElement . PHP_EOL;
        $body .= $this->getChildrenJrXml();
        $closeTag = '</frame>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        if ($generator->evaluatePrintWhenExpression($this->printWhenExpression)) {
            parent::generate($generator, $processor);
        }
    }
}

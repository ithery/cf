<?php

class CReport_Builder_Element_ColumnFooter extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        foreach ($xml as $tag => $bandElement) {
            if ($tag == 'band') {
                $element->setBandPropertyFromXml($bandElement);
                $element->addChildrenFromXml($bandElement);
            }
        }

        return $element;
    }

    public function toJrXml() {
        $openTag = '<columnFooter>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</columnFooter>';
        // cdbg::dd($body);
        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $height = $this->getHeight();
        //$processor->preventYOverflow($generator, $height);

        parent::generate($generator, $processor);
        $generator->setColumnFooterDrawn();
        $processor->addY($height);
    }
}

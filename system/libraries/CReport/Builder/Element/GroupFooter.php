<?php

class CReport_Builder_Element_GroupFooter extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<groupFooter>';

        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</groupFooter>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
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

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $height = $this->getHeight();
        parent::generate($generator, $processor);
        $processor->preventYOverflow($generator, $height);

        $processor->addY($height);
    }
}

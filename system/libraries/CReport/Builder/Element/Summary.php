<?php

class CReport_Builder_Element_Summary extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        $band = $xml->band;
        if ($band) {
            $element->setBandPropertyFromXml($band);
            $element->addChildrenFromXml($band);
        }

        return $element;
    }

    public function toJrXml() {
        $openTag = '<summary>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</summary>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
        // CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
        $height = $this->getHeight();
        $processor->addY($height);
    }
}

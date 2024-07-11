<?php

class CReport_Builder_Element_Title extends CReport_Builder_ElementAbstract {
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
        $openTag = '<title>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</title>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
        // CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
        $height = $this->getHeight();
        $processor->addY($height);
    }
}

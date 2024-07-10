<?php

class CReport_Builder_Element_GroupHeader extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<groupHeader>';

        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</groupHeader>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
        $height = $this->getHeight();
        $processor->addY($height);
    }
}

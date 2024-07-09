<?php

class CReport_Builder_Element_PageHeader extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<pageHeader>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</pageHeader>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator_ProcessorAbstract $processor) {

    }
}

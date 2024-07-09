<?php

class CReport_Builder_Element_ColumnHeader extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<columnHeader>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</columnHeader>';
        // cdbg::dd($body);
        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator_ProcessorAbstract $processor) {

    }
}

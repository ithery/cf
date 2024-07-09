<?php

class CReport_Builder_Element_Detail extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<detail>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</detail>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

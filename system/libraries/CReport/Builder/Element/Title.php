<?php

class CReport_Builder_Element_Title extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<title>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</title>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

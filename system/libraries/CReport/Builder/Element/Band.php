<?php

class CReport_Builder_Element_Band extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_SplitTypePropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
        $this->splitType = null;
    }

    public function toJrXml() {
        $openTag = '<band';
        if ($this->height !== null) {
            $openTag .= ' height="' . $this->height . '"';
        }
        if ($this->splitType !== null) {
            $openTag .= ' splitType="' . CReport_Builder_JrXmlEnum::getSplitTypeEnum($this->splitType) . '"';
        }
        $openTag .= '>';
        $body = $this->getChildrenJrXml();
        $closeTag = '</band>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

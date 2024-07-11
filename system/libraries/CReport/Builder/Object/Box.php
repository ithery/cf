<?php

class CReport_Builder_Object_Box implements CReport_Builder_Contract_JrXmlElementInterface {
    use CReport_Builder_Trait_Property_PaddingPropertyTrait;
    use CReport_Builder_Trait_Property_PenPropertyTrait;
    use CReport_Builder_Trait_Property_TopPenPropertyTrait;
    use CReport_Builder_Trait_Property_BottomPenPropertyTrait;
    use CReport_Builder_Trait_Property_LeftPenPropertyTrait;
    use CReport_Builder_Trait_Property_RightPenPropertyTrait;

    public function __construct() {
        $this->padding = new CReport_Builder_Object_Padding();
        $this->pen = null;
        $this->topPen = null;
        $this->leftPen = null;
        $this->rightPen = null;
        $this->bottomPen = null;
    }

    public function toJrXml() {
        $xml = '';
        $xml .= '<box>' . PHP_EOL;
        if ($this->pen) {
            $xml .= $this->pen->toJrXml();
        }
        if ($this->topPen) {
            $xml .= $this->topPen->toJrXml('topPen');
        }
        if ($this->rightPen) {
            $xml .= $this->rightPen->toJrXml('rightPen');
        }
        if ($this->bottomPen) {
            $xml .= $this->bottomPen->toJrXml('bottomPen');
        }
        if ($this->leftPen) {
            $xml .= $this->leftPen->toJrXml('leftPen');
        }
        $xml .= $this->padding->toJrXml();

        $xml .= '</box>' . PHP_EOL;

        return $xml;
    }
}

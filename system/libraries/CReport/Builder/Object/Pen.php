<?php

class CReport_Builder_Object_Pen implements CReport_Builder_Contract_JrXmlElementInterface {
    use CReport_Builder_Trait_Property_LineWidthPropertyTrait;
    use CReport_Builder_Trait_Property_LineStylePropertyTrait;
    use CReport_Builder_Trait_Property_LineColorPropertyTrait;

    public function __construct($top = 0, $right = 0, $bottom = 0, $left = 0) {
        $this->lineWidth = 0;
        $this->lineStyle = CReport::LINE_STYLE_SOLID;
        $this->lineColor = '#000000';
    }

    public function toJrXml() {
        $tag = '<pen ';
        $tag .= ' lineWidth="' . $this->lineWidth . '"';
        $tag .= ' lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->lineStyle) . '"';
        $tag .= ' lineColor="' . $this->lineColor . '"';
        $tag .= '/>';

        return $tag;
    }
}

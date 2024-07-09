<?php

class CReport_Builder_Object_Pen implements CReport_Builder_Contract_JrXmlElementInterface {
    use CReport_Builder_Trait_Property_LineWidthPropertyTrait;
    use CReport_Builder_Trait_Property_LineStylePropertyTrait;
    use CReport_Builder_Trait_Property_LineColorPropertyTrait;

    public function __construct($lineWidth = 0, $lineStyle = CReport::LINE_STYLE_SOLID, $lineColor = '#000000') {
        $this->lineWidth = $lineWidth;
        $this->lineStyle = $lineStyle;
        $this->lineColor = $lineColor;
    }

    public function toJrXml($tag = 'pen') {
        $xml = '<' . $tag . ' ';
        $xml .= ' lineWidth="' . $this->lineWidth . '"';
        $xml .= ' lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->lineStyle) . '"';
        $xml .= ' lineColor="' . $this->lineColor . '"';
        $xml .= '/>';

        return $xml;
    }
}

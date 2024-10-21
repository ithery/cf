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
        $xml = '<' . $tag . '';
        $xml .= ' lineWidth="' . $this->lineWidth . '"';
        $xml .= ' lineStyle="' . CReport_Builder_PhpToJrXmlEnum::getLineStyleEnum($this->lineStyle) . '"';
        $xml .= ' lineColor="' . $this->lineColor . '"';
        $xml .= '/>';

        return $xml;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $font = new self();
        if ($xml['lineWidth']) {
            $font->setLineWidth((float) $xml['lineWidth']);
        }
        if ($xml['lineStyle']) {
            $font->setLineStyle(CReport_Builder_JrXmlToPhpEnum::getLineStyleEnum((string) $xml['lineStype']));
        }
        if ($xml['lineColor']) {
            $font->setLineColor((string) $xml['lineColor']);
        }

        return $font;
    }
}

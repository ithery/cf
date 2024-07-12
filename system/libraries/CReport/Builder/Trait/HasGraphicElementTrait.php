<?php

trait CReport_Builder_Trait_HasGraphicElementTrait {
    use CReport_Builder_Trait_Property_PenPropertyTrait;

    public function getGraphicElementJrXml() {
        $textElement = '<graphicElement>';
        $textElement .= $this->pen->toJrXml();
        $textElement .= '</graphicElement>';

        return $textElement;
    }

    public function setGraphicElementPropertyFromXml(SimpleXMLElement $textElement) {
        foreach ($textElement as $tag => $xmlElement) {
            if ($tag == 'pen') {
                $this->pen = CReport_Builder_Object_Pen::fromXml($xmlElement);
            }
        }
    }
}

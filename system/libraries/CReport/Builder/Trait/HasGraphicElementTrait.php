<?php

trait CReport_Builder_Trait_HasGraphicElementTrait {
    use CReport_Builder_Trait_Property_PenPropertyTrait;
    use CReport_Builder_Trait_Property_RadiusPropertyTrait;

    public function getGraphicElementJrXml() {
        $textElement = '<graphicElement>';
        $textElement .= $this->pen->toJrXml();
        if ($this->radius !== null) {
            $textElement .= '<radius>' . $this->getRadius() . '</radius>';
        }
        $textElement .= '</graphicElement>';

        return $textElement;
    }

    public function setGraphicElementPropertyFromXml(SimpleXMLElement $textElement) {
        foreach ($textElement as $tag => $xmlElement) {
            if ($tag == 'pen') {
                $this->pen = CReport_Builder_Object_Pen::fromXml($xmlElement);
            }
            if ($tag == 'radius') {
                $this->radius = (float) $xmlElement;
            }
        }
    }
}

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
        $this->pen = new CReport_Builder_Object_Pen();
        $this->topPen = new CReport_Builder_Object_Pen();
        $this->leftPen = new CReport_Builder_Object_Pen();
        $this->rightPen = new CReport_Builder_Object_Pen();
        $this->bottomPen = new CReport_Builder_Object_Pen();
    }

    public function toJrXml() {
        $xml = '';
        $xml .= '<box>' . PHP_EOL;
        $xml .= '<pen
            lineWidth="' . $this->pen->getLineWidth() . '"
            lineColor="' . $this->pen->getLineColor() . '"
            lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->pen->getLineStyle()) . '"
        />' . PHP_EOL;
        $xml .= '<topPen
            lineWidth="' . $this->topPen->getLineWidth() . '"
            lineColor="' . $this->topPen->getLineColor() . '"
            lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->topPen->getLineStyle()) . '"
        />' . PHP_EOL;
        $xml .= '<rightPen
            lineWidth="' . $this->rightPen->getLineWidth() . '"
            lineColor="' . $this->rightPen->getLineColor() . '"
            lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->rightPen->getLineStyle()) . '"
        />' . PHP_EOL;
        $xml .= '<bottomPen
            lineWidth="' . $this->bottomPen->getLineWidth() . '"
            lineColor="' . $this->bottomPen->getLineColor() . '"
            lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->bottomPen->getLineStyle()) . '"
        />' . PHP_EOL;
        $xml .= '<leftPen
            lineWidth="' . $this->leftPen->getLineWidth() . '"
            lineColor="' . $this->leftPen->getLineColor() . '"
            lineStyle="' . CReport_Builder_JrXmlEnum::getLineStyleEnum($this->leftPen->getLineStyle()) . '"
        />' . PHP_EOL;
        $xml .= $this->padding->toJrXml();

        $xml .= '</box>' . PHP_EOL;

        return $xml;
    }
}

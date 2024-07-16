<?php

trait CReport_Builder_Trait_HasReportElementTrait {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_WidthPropertyTrait;
    use CReport_Builder_Trait_Property_XPropertyTrait;
    use CReport_Builder_Trait_Property_YPropertyTrait;
    use CReport_Builder_Trait_Property_BackgroundColorPropertyTrait;
    use CReport_Builder_Trait_Property_ForegroundColorPropertyTrait;
    use CReport_Builder_Trait_Property_ModePropertyTrait;
    use CReport_Builder_Trait_Property_PrintWhenExpressionPropertyTrait;

    public function getReportElementJrXml() {
        $reportElement = '<reportElement';
        if ($this->x !== null) {
            $reportElement .= ' x="' . $this->x . '"';
        }
        if ($this->y !== null) {
            $reportElement .= ' y="' . $this->y . '"';
        }
        if ($this->width !== null) {
            $reportElement .= ' width="' . $this->width . '"';
        }
        if ($this->height !== null) {
            $reportElement .= ' height="' . $this->height . '"';
        }
        if ($this->foregroundColor !== null) {
            $reportElement .= ' forecolor="' . $this->foregroundColor . '"';
        }
        if ($this->backgroundColor !== null) {
            $reportElement .= ' backcolor="' . $this->backgroundColor . '"';
        }
        if ($this->mode !== null) {
            $reportElement .= ' mode="' . CReport_Builder_PhpToJrXmlEnum::getModeEnum($this->mode) . '"';
        }
        $reportElement .= '>' . PHP_EOL;
        if ($this->printWhenExpression) {
            $reportElement .= '<printWhenExpression><![CDATA[' . $this->printWhenExpression . ']]></printWhenExpression>' . PHP_EOL;
        }
        $reportElement .= '</reportElement>' . PHP_EOL;

        return $reportElement;
    }

    public function setReportElementPropertyFromXml(SimpleXMLElement $reportElement) {
        if ($reportElement['x']) {
            $this->setX((float) $reportElement['x']);
        }
        if ($reportElement['y']) {
            $this->setY((float) $reportElement['y']);
        }
        if ($reportElement['width']) {
            $this->setWidth((float) $reportElement['width']);
        }
        if ($reportElement['height']) {
            $this->setHeight((float) $reportElement['height']);
        }
        if ($reportElement['backcolor']) {
            $this->setBackgroundColor((string) $reportElement['backcolor']);
        }
        if ($reportElement['forecolor']) {
            $this->setForegroundColor((string) $reportElement['forecolor']);
        }
        if ($reportElement['mode']) {
            $this->setMode(CReport_Builder_JrXmlToPhpEnum::getModeEnum((string) $reportElement['mode']));
        }
        foreach ($reportElement as $tag => $xmlElement) {
            if ($tag == 'printWhenExpression') {
                $this->setPrintWhenExpression((string) $xmlElement);
            }
        }
    }
}

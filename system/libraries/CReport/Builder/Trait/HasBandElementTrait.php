<?php

trait CReport_Builder_Trait_HasBandElementTrait {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_SplitTypePropertyTrait;
    use CReport_Builder_Trait_Property_PrintWhenExpressionPropertyTrait;

    public function jrXmlWrapWithBand(string $body) {
        $openTag = '<band';
        if ($this->height) {
            $openTag .= $this->height !== null ? ' height="' . $this->height . '"' : '';
        }

        if ($this->splitType) {
            $openTag .= $this->splitType !== null ? ' splitType="' . CReport_Builder_PhpToJrXmlEnum::getSplitTypeEnum($this->splitType) . '"' : '';
        }

        $openTag .= '>';
        if ($this->printWhenExpression) {
            $openTag .= PHP_EOL;
            $openTag .= '<printWhenExpression><![CDATA[' . $this->printWhenExpression . ']]></printWhenExpression>' . PHP_EOL;
        }
        $closeTag = '</band>';

        return $openTag . $body . $closeTag;
    }

    public function setBandPropertyFromXml(SimpleXMLElement $band) {
        if ($band['height']) {
            $this->setHeight((float) $band['height']);
        }
        if ($band['splitType']) {
            $this->setSplitType(CReport_Builder_JrXmlToPhpEnum::getSplitTypeEnum((string) $band['splitType']));
        }
        foreach ($band as $tag => $xmlElement) {
            if ($tag == 'printWhenExpression') {
                $this->setPrintWhenExpression((string) $xmlElement);
            }
        }
    }
}

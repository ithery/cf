<?php

class CReport_Builder_Element_StaticText extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasReportElementTrait;
    use CReport_Builder_Trait_HasTextElementTrait;
    use CReport_Builder_Trait_Property_TextPropertyTrait;
    use CReport_Builder_Trait_Property_BoxPropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
        $this->box = new CReport_Builder_Object_Box();
        $this->font = new CReport_Builder_Object_Font();
        $this->paragraph = new CReport_Builder_Object_Paragraph();
        $this->textAlignment = CReport::TEXT_ALIGNMENT_LEFT;
        $this->verticalAlignment = CReport::VERTICAL_ALIGNMENT_TOP;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'reportElement') {
                $element->setReportElementPropertyFromXml($xmlElement);
            }
            if ($tag == 'text') {
                $element->setText((string) $xmlElement);
            }
        }

        return $element;
    }

    public function toJrXml() {
        $openTag = '<staticText>';

        $reportElement = $this->getReportElementJrXml();
        $textElement = $this->getTextElementJrXml();
        $text = '<text><![CDATA[' . $this->text . ']]></text>' . PHP_EOL;
        $body = '';
        $body .= $reportElement . PHP_EOL;
        $body .= $this->box->toJrXml() . PHP_EOL;
        $body .= $textElement . PHP_EOL;
        $body .= $text . PHP_EOL;
        $body .= $this->getChildrenJrXml();
        $closeTag = '</staticText>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        if ($generator->evaluatePrintWhenExpression($this->printWhenExpression)) {
            $options = [];
            $options['x'] = $this->getX();
            $options['y'] = $this->getY();
            $options['width'] = $this->getWidth();
            $options['height'] = $this->getHeight();
            $options['text'] = $this->getText();
            $options['textAlignment'] = $this->getTextAlignment();
            $options['verticalAlignment'] = $this->getVerticalAlignment();
            $options['font'] = $this->getFont();
            $options['backgroundColor'] = $this->getBackgroundColor();
            $options['box'] = $this->getBox();
            $options['lineSpacing'] = $this->getLineSpacing();
            $processor->cell($options);
        }
    }
}

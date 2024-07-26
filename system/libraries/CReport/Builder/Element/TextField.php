<?php

class CReport_Builder_Element_TextField extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasReportElementTrait;
    use CReport_Builder_Trait_HasTextElementTrait;
    use CReport_Builder_Trait_Property_TextPropertyTrait;
    use CReport_Builder_Trait_Property_BoxPropertyTrait;
    use CReport_Builder_Trait_Property_IsStretchWithOverflowPropertyTrait;
    use CReport_Builder_Trait_Property_PatternPropertyTrait;

    /**
     * @var string
     */
    protected $textFieldExpression;

    protected $forceHeight;

    public function __construct() {
        parent::__construct();
        $this->height = null;
        $this->box = new CReport_Builder_Object_Box();
        $this->font = new CReport_Builder_Object_Font();
        $this->paragraph = new CReport_Builder_Object_Paragraph();
        $this->textAlignment = CReport::TEXT_ALIGNMENT_LEFT;
        $this->verticalAlignment = CReport::VERTICAL_ALIGNMENT_TOP;
        $this->mode = CReport::MODE_OPAQUE;
        $this->isStretchWithOverflow = false;
        $this->textFieldExpression = '';
    }

    public function forceHeight($height) {
        $this->forceHeight = $height;
    }

    public function unforceHeight() {
        $this->forceHeight = null;
    }

    /**
     * @param string $expression
     *
     * @return $this
     */
    public function setTextFieldExpression($expression) {
        $this->textFieldExpression = $expression;

        return $this;
    }

    /**
     * @return string
     */
    public function getTextFieldExpression() {
        return $this->textFieldExpression;
    }

    public function getHeightForGenerate() {
        return $this->forceHeight !== null ? $this->forceHeight : $this->height;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();
        if ($xml['isStretchWithOverflow']) {
            $element->setStretchWithOverflow(CReport_Builder_JrXmlToPhpEnum::getBoolEnum((string) $xml['isStretchWithOverflow']));
        }
        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'reportElement') {
                $element->setReportElementPropertyFromXml($xmlElement);
            }
            if ($tag == 'textElement') {
                $element->setTextElementPropertyFromXml($xmlElement);
            }
            if ($tag == 'box') {
                $element->setBox(CReport_Builder_Object_Box::fromXml($xmlElement));
            }
            if ($tag == 'text') {
                $element->setText((string) $xmlElement);
            }
            if ($tag == 'textFieldExpression') {
                $element->setTextFieldExpression((string) $xmlElement);
            }
            if ($tag == 'pattern') {
                $element->setPattern((string) $xmlElement);
            }
        }

        return $element;
    }

    public function toJrXml() {
        $openTag = '<textField isStretchWithOverflow="' . CReport_Builder_PhpToJrXmlEnum::getBoolEnum($this->isStretchWithOverflow) . '">';

        $reportElement = $this->getReportElementJrXml();

        $textElement = $this->getTextElementJrXml();
        $textFieldExpression = '<textFieldExpression><![CDATA[' . $this->textFieldExpression . ']]></textFieldExpression>' . PHP_EOL;
        $pattern = '';
        if ($this->pattern) {
            $pattern = '<pattern>' . $this->pattern . '</pattern>';
        }
        $body = '';
        $body .= $reportElement . PHP_EOL;
        $body .= $this->box->toJrXml() . PHP_EOL;
        $body .= $textElement . PHP_EOL;
        $body .= $textFieldExpression . PHP_EOL;
        $body .= $pattern;
        $body .= $this->getChildrenJrXml();
        $closeTag = '</textField>';

        $xml = $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;

        return $xml;
    }

    private function getTextAfterExpression(CReport_Generator $generator) {
        $text = $this->getTextFieldExpression();

        if ($text) {
            $text = $generator->getExpression($text);
        } else {
            $text = $this->getText();
        }
        $pattern = $this->getPattern();
        if ($pattern) {
            $textToFormat = $text;
            if ($textToFormat == '0') {
                $textToFormat = 0;
            }
            $text = $generator->formatPattern($textToFormat, $pattern);
        }

        return $text;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        if ($generator->evaluatePrintWhenExpression($this->printWhenExpression)) {
            $text = $this->getTextAfterExpression($generator);
            if ($this->style) {
                $this->applyStyle($generator);
            }
            $options = [];
            $options['x'] = $this->getX();
            $options['y'] = $this->getY();
            $options['width'] = $this->getWidth();
            $options['height'] = $this->getHeightForGenerate();
            $options['text'] = $text;
            $options['textAlignment'] = $this->getTextAlignment();
            $options['verticalAlignment'] = $this->getVerticalAlignment();
            $options['font'] = $this->getFont();
            $options['backgroundColor'] = $this->getBackgroundColor();
            $options['foregroundColor'] = $this->getForegroundColor();
            $options['box'] = $this->getBox();
            $options['lineSpacing'] = $this->getLineSpacing();
            $options['mode'] = $this->getMode();
            if (strpos($text, '$V{PAGE_COUNT}') !== false) {
                $generator->addInstruction($processor, function (CReport_Generator_ProcessorAbstract $processor) use ($generator, $text, $options) {
                    $text = $generator->getExpression($text, CREPORT::EVALUATION_TIME_REPORT);
                    $options['text'] = $text;
                    $processor->cell($options);
                });
            } else {
                $processor->cell($options);
            }
        }
    }

    public function getCellHeight(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        if (!$generator->evaluatePrintWhenExpression($this->printWhenExpression)) {
            return 0;
        }
        $text = $this->getTextAfterExpression($generator);
        $options = [];
        $options['x'] = $this->getX();
        $options['y'] = $this->getY();
        $options['width'] = $this->getWidth();
        $options['height'] = $this->getHeight();
        $options['text'] = $text;
        $options['textAlignment'] = $this->getTextAlignment();
        $options['verticalAlignment'] = $this->getVerticalAlignment();
        $options['font'] = $this->getFont();
        $options['backgroundColor'] = $this->getBackgroundColor();
        $options['box'] = $this->getBox();
        $options['lineSpacing'] = $this->getLineSpacing();

        return $processor->cellHeight($options);
    }
}

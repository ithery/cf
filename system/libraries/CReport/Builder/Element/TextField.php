<?php

class CReport_Builder_Element_TextField extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasReportElementTrait;
    use CReport_Builder_Trait_Property_TextPropertyTrait;
    use CReport_Builder_Trait_Property_BoxPropertyTrait;
    use CReport_Builder_Trait_Property_FontPropertyTrait;
    use CReport_Builder_Trait_Property_ParagraphPropertyTrait;
    use CReport_Builder_Trait_Property_PrintWhenExpressionPropertyTrait;
    use CReport_Builder_Trait_Property_TextAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_VerticalAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_IsStretchWithOverflowPropertyTrait;
    use CReport_Builder_Trait_Property_PatternPropertyTrait;
    /**
     * @var string
     */
    protected $textFieldExpression;

    public function __construct() {
        parent::__construct();
        $this->height = null;
        $this->box = new CReport_Builder_Object_Box();
        $this->font = new CReport_Builder_Object_Font();
        $this->paragraph = new CReport_Builder_Object_Paragraph();
        $this->textAlignment = CReport::TEXT_ALIGNMENT_LEFT;
        $this->verticalAlignment = CReport::VERTICAL_ALIGNMENT_TOP;
        $this->isStretchWithOverflow = false;
        $this->textFieldExpression = '';
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

    public function toJrXml() {
        $openTag = '<textField isStretchWithOverflow="' . CReport_Builder_JrXmlEnum::getBoolEnum($this->isStretchWithOverflow) . '">';

        $reportElement = $this->getReportElementJrXml();

        $textElement = '<textElement textAlignment="' . $this->textAlignment . '" verticalAlignment="' . $this->verticalAlignment . '">
        ' . $this->font->toJrXml() . '
        ' . $this->paragraph->toJrXml() . '
        </textElement>';
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

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator_ProcessorAbstract $processor) {
    }
}

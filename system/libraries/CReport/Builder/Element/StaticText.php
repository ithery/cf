<?php

class CReport_Builder_Element_StaticText extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_WidthPropertyTrait;
    use CReport_Builder_Trait_Property_XPropertyTrait;
    use CReport_Builder_Trait_Property_YPropertyTrait;
    use CReport_Builder_Trait_Property_BackgroundColorPropertyTrait;
    use CReport_Builder_Trait_Property_TextPropertyTrait;
    use CReport_Builder_Trait_Property_BoxPropertyTrait;
    use CReport_Builder_Trait_Property_FontPropertyTrait;
    use CReport_Builder_Trait_Property_ParagraphPropertyTrait;
    use CReport_Builder_Trait_Property_PrintWhenExpressionPropertyTrait;
    use CReport_Builder_Trait_Property_TextAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_VerticalAlignmentPropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
        $this->box = new CReport_Builder_Object_Box();
        $this->font = new CReport_Builder_Object_Font();
        $this->paragraph = new CReport_Builder_Object_Paragraph();
        $this->textAlignment = CReport::TEXT_ALIGNMENT_LEFT;
        $this->verticalAlignment = CReport::VERTICAL_ALIGNMENT_TOP;
    }

    public function toJrXml() {
        $openTag = '<staticText>';

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
        if ($this->backgroundColor !== null) {
            $reportElement .= ' backcolor="' . $this->backgroundColor . '" mode="Opaque"';
        }
        $reportElement .= '>' . PHP_EOL;
        if ($this->printWhenExpression) {
            $reportElement .= '<printWhenExpression><![CDATA[' . $this->printWhenExpression . ']]></printWhenExpression>' . PHP_EOL;
        }
        $reportElement .= '</reportElement>' . PHP_EOL;
        $textElement = '<textElement textAlignment="' . $this->textAlignment . '" verticalAlignment="' . $this->verticalAlignment . '">
        ' . $this->font->toJrXml() . '
        ' . $this->paragraph->toJrXml() . '
        </textElement>';
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

    public function generate(CReport_Generator_ProcessorAbstract $processor) {
        $options = [];
        $options['x'] = $this->getX();
        $options['y'] = $this->getY();
        $options['width'] = $this->getWidth();
        $options['height'] = $this->getHeight();
        $options['text'] = $this->getText();
        $options['textAlignment'] = $this->getTextAlignment();
        $options['font'] = $this->getFont();
        $options['backgroundColor'] = $this->getBackgroundColor();
        $processor->cell($options);
    }
}

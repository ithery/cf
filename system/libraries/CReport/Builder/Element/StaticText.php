<?php

class CReport_Builder_Element_StaticText extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_WidthPropertyTrait;
    use CReport_Builder_Trait_Property_XPropertyTrait;
    use CReport_Builder_Trait_Property_YPropertyTrait;
    use CReport_Builder_Trait_Property_BackgroundColorPropertyTrait;
    use CReport_Builder_Trait_Property_TextPropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
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
            $reportElement .= ' backColor="' . $this->backgroundColor . '" mode="Opaque"';
        }
        $textElement = '<text><![CDATA[' . $this->text . ']]></text>' . PHP_EOL;
        $reportElement .= '>' . PHP_EOL;
        $reportElement .= '</reportElement>' . PHP_EOL;
        $body = '';
        $body .= $reportElement . PHP_EOL;
        $body .= $textElement . PHP_EOL;
        $body .= $this->getChildrenJrXml();
        $closeTag = '</staticText>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

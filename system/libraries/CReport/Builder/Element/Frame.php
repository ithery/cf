<?php

class CReport_Builder_Element_Frame extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_WidthPropertyTrait;
    use CReport_Builder_Trait_Property_XPropertyTrait;
    use CReport_Builder_Trait_Property_YPropertyTrait;
    use CReport_Builder_Trait_Property_BackgroundColorPropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
    }

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<frame>';

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
        $reportElement .= '>' . PHP_EOL;
        // $xml = '';
        // $xml .= '<frame>' . PHP_EOL;
        // $xml .= '<reportElement x="' . $this->xOffset . '" y="' . $this->yOffset . '" width="515" height="100" backcolor="' . $this->backColor . '" mode="Opaque"/>' . PHP_EOL;

        $reportElement .= '</reportElement>' . PHP_EOL;

        // $imageExpression = '<imageExpression><![CDATA["' . $this->src . '"]]></imageExpression>';
        $body = $reportElement . PHP_EOL;
        $body .= $this->getChildrenJrXml();
        $closeTag = '</frame>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

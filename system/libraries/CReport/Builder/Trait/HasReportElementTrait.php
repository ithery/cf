<?php

trait CReport_Builder_Trait_HasReportElementTrait {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_WidthPropertyTrait;
    use CReport_Builder_Trait_Property_XPropertyTrait;
    use CReport_Builder_Trait_Property_YPropertyTrait;
    use CReport_Builder_Trait_Property_BackgroundColorPropertyTrait;
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
        if ($this->backgroundColor !== null) {
            $reportElement .= ' backcolor="' . $this->backgroundColor . '" mode="Opaque"';
        }
        $reportElement .= '>' . PHP_EOL;
        if ($this->printWhenExpression) {
            $reportElement .= '<printWhenExpression><![CDATA[' . $this->printWhenExpression . ']]></printWhenExpression>' . PHP_EOL;
        }
        $reportElement .= '</reportElement>' . PHP_EOL;

        return $reportElement;
    }
}

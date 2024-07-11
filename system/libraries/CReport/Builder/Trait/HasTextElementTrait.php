<?php

trait CReport_Builder_Trait_HasTextElementTrait {
    use CReport_Builder_Trait_Property_FontPropertyTrait;
    use CReport_Builder_Trait_Property_ParagraphPropertyTrait;
    use CReport_Builder_Trait_Property_TextAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_VerticalAlignmentPropertyTrait;

    public function getTextElementJrXml() {
        $textElement = '<textElement textAlignment="' . CReport_Builder_PhpToJrXmlEnum::getTextAlignmentEnum($this->textAlignment) . '" verticalAlignment="' . CReport_Builder_PhpToJrXmlEnum::getVerticalAlignmentEnum($this->verticalAlignment) . '">';
        $textElement .= $this->font->toJrXml();
        $textElement .= $this->paragraph->toJrXml();
        $textElement .= '</textElement>';

        return $textElement;
    }
}

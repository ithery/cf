<?php

trait CReport_Builder_Trait_HasTextElementTrait {
    use CReport_Builder_Trait_Property_FontPropertyTrait;
    use CReport_Builder_Trait_Property_ParagraphPropertyTrait;
    use CReport_Builder_Trait_Property_TextAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_VerticalAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_LetterSpacingPropertyTrait;
    use CReport_Builder_Trait_Property_WordSpacingPropertyTrait;

    public function getTextElementJrXml() {
        $textElement = '<textElement textAlignment="' . CReport_Builder_PhpToJrXmlEnum::getTextAlignmentEnum($this->textAlignment) . '" verticalAlignment="' . CReport_Builder_PhpToJrXmlEnum::getVerticalAlignmentEnum($this->verticalAlignment);
        if ($this->letterSpacing) {
            $textElement .= ' letterSpacing="' . $this->getLetterSpacing() . '"';
        }
        if ($this->wordSpacing) {
            $textElement .= ' wordSpacing="' . $this->getWordSpacing() . '"';
        }
        $textElement .= '">';
        $textElement .= $this->font->toJrXml();
        $textElement .= $this->paragraph->toJrXml();
        $textElement .= '</textElement>';

        return $textElement;
    }

    public function setTextElementPropertyFromXml(SimpleXMLElement $textElement) {
        if ($textElement['textAlignment']) {
            $this->setTextAlignment(CReport_Builder_JrXmlToPhpEnum::getTextAlignmentEnum((string) $textElement['textAlignment']));
        }
        if ($textElement['letterSpacing']) {
            $this->setLetterSpacing((float) $textElement['letterSpacing']);
        }
        if ($textElement['wordSpacing']) {
            $this->setWordSpacing((float) $textElement['wordSpacing']);
        }
        if ($textElement['verticalAlignment']) {
            $this->setVerticalAlignment(CReport_Builder_JrXmlToPhpEnum::getVerticalAlignmentEnum((string) $textElement['verticalAlignment']));
        }
        foreach ($textElement as $tag => $xmlElement) {
            if ($tag == 'font') {
                $this->font = CReport_Builder_Object_Font::fromXml($xmlElement);
            }
            if ($tag == 'paragraph') {
                $this->paragraph = CReport_Builder_Object_Paragraph::fromXml($xmlElement);
            }
        }
    }
}

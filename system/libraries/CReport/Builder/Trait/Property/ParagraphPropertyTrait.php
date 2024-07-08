<?php

trait CReport_Builder_Trait_Property_ParagraphPropertyTrait {
    /**
     * @var CReport_Builder_Object_Paragraph
     */
    protected $paragraph;

    /**
     * @return CReport_Builder_Object_Paragraph
     */
    public function getParagraph() {
        return $this->paragraph;
    }

    /**
     * @param CReport_Builder_Object_Paragraph $paragraph
     *
     * @return $this
     */
    public function setParagraph(CReport_Builder_Object_Paragraph $paragraph) {
        $this->paragraph = $paragraph;

        return $this;
    }

    /**
     * @param float $lineSpacing
     *
     * @return $this
     */
    public function setLineSpacing($lineSpacing) {
        $this->paragraph->setLineSpacing($lineSpacing);

        return $this;
    }

    /**
     * @return float
     */
    public function getLineSpacing() {
        return $this->paragraph->getLineSpacing();
    }
}

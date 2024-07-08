<?php

class CReport_Builder_Object_Paragraph implements CReport_Builder_Contract_JrXmlElementInterface {
    /**
     * @var float
     */
    protected $lineSpacing;

    public function __construct() {
        $this->lineSpacing = 1;
    }

    /**
     * @param float $lineSpacing
     *
     * @return $this
     */
    public function setLineSpacing($lineSpacing) {
        $this->lineSpacing = $lineSpacing;

        return $this;
    }

    /**
     * @return float
     */
    public function getLineSpacing() {
        return $this->lineSpacing;
    }

    public function toJrXml() {
        $tag = '<paragraph';
        if ($this->lineSpacing) {
            $tag .= ' lineSpacing="' . $this->lineSpacing . '"';
        }

        $tag .= '/>';

        return $tag;
    }
}

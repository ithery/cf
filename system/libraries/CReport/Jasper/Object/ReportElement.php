<?php

class CReport_Jasper_Object_ReportElement {
    protected $xmlElement;

    public function __construct(SimpleXMLElement $xmlElement) {
        $this->xmlElement = $xmlElement;
    }

    /**
     * @return null|flaot
     */
    public function getHeight() {
        $height = $this->xmlElement['height'];
        if ($height != null) {
            $height = (float) $height;
        }

        return $height;
    }
}

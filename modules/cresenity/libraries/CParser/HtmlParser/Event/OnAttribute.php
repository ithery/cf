<?php

class CParser_HtmlParser_Event_OnAttribute {
    public $attributeName;
    public $attributeValue;

    public function __construct($attributeName, $attributeValue) {
        $this->attributeName = $attributeName;
        $this->attributeValue = $attributeValue;
    }
}

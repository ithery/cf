<?php

class CReport_Builder_Element_Detail extends CReport_Builder_ElementAbstract {
    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<detail>';
        $body = $this->getChildrenJrXml();
        $closeTag = '</detail>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

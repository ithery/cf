<?php

class CReport_Builder_Element_PageHeader extends CReport_Builder_ElementAbstract {
    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<pageHeader>';
        $body = $this->getChildrenJrXml();
        $closeTag = '</pageHeader>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

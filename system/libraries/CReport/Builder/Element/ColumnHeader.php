<?php

class CReport_Builder_Element_ColumnHeader extends CReport_Builder_ElementAbstract {
    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<columnHeader>';
        $body = $this->getChildrenJrXml();
        $closeTag = '</columnHeader>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

<?php

abstract class CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasChildrenElementTrait;
    protected $children;

    public function __construct() {
        $this->children = [];
    }

    protected function getChildrenJrXml() {
        $xml = '';
        foreach ($this->children as $child) {
            $xml .= $child->toJrXml() . PHP_EOL;
        }

        return $xml;
    }
}

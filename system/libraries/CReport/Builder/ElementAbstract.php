<?php

abstract class CReport_Builder_ElementAbstract implements CReport_Builder_Contract_JrXmlElementInterface {
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

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        foreach ($this->children as $child) {
            $child->generate($generator, $processor);
        }
    }
}

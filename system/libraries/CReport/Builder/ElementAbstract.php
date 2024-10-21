<?php

abstract class CReport_Builder_ElementAbstract implements CReport_Builder_Contract_JrXmlElementInterface {
    use CReport_Builder_Trait_HasChildrenElementTrait;

    protected $children;

    public function __construct() {
        $this->children = [];
    }

    public function addChildrenFromXml(SimpleXMLElement $xml) {
        foreach ($xml as $tag => $xmlElement) {
            if (!CReport_Builder_ElementFactory::isIgnore($tag)) {
                $this->addChildren(CReport_Builder_ElementFactory::createElementFromXml($tag, $xmlElement));
            }
        }

        return $this;
    }

    public function addChildren(CReport_Builder_ElementAbstract $element) {
        $this->children[] = $element;

        return $this;
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

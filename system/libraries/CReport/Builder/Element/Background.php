<?php

class CReport_Builder_Element_Background extends CReport_Builder_ElementAbstract {
    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<background>';

        $body = $this->getChildrenJrXml();
        $closeTag = '</background>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
    }
}

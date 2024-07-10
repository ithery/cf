<?php

class CReport_Builder_Element_Detail extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasBandElementTrait;

    public function __construct() {
        parent::__construct();
    }

    public function toJrXml() {
        $openTag = '<detail>';
        $body = $this->jrXmlWrapWithBand($this->getChildrenJrXml());
        $closeTag = '</detail>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $data = $generator->getData();
        if (count($this->children) > 0) {
            foreach ($data as $rowIndex => $row) {
                $height = $this->getHeight();
                /** @var CReport_Builder_Row $row */
                $generator->setCurrentRow($row);
                $processor->preventYOverflow($generator, $height);
                foreach ($this->children as $child) {
                    $child->generate($generator, $processor);
                }
                //if ($this->getSplitType() == CREPORT::SPLIT_TYPE_STRETCH || $this->getSplitType() == CREPORT::SPLIT_TYPE_PREVENT) {

                    $processor->addY($height);
                //}
            }
        }
    }
}

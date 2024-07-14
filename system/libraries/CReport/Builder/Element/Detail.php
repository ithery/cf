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

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        foreach ($xml as $tag => $bandElement) {
            if ($tag == 'band') {
                $element->setBandPropertyFromXml($bandElement);
                $element->addChildrenFromXml($bandElement);
            }
        }

        return $element;
    }

    protected function generaterGroupHeaders(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor, $rowIndex, $row, $lastRow) {
        foreach ($generator->getGroups() as $group) {
            /** @var CReport_Builder_Element_Group $group */
            preg_match_all("/F{(\w+)}/", $group->getGroupExpression(), $matchesF);
            $groupExpression = $matchesF[1][0];
            $shouldRender = false;
            if ($lastRow) {
                $lastGroupValue = carr::get($lastRow, $groupExpression);
                $groupValue = carr::get($row, $groupExpression);
                if ($lastGroupValue != $groupValue) {
                    $shouldRender = true;
                }
            }

            if (($rowIndex == 0 || $shouldRender) && ($group->hasGroupHeader())) {
                $groupHeader = $group->getGroupHeaderElement();
                $groupHeader->generate($generator, $processor);
            }
        }
    }

    protected function generaterGroupFooters(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor, $rowIndex, $row, $nextRow) {
        foreach ($generator->getReport()->getGroupElements() as $group) {
            /** @var CReport_Builder_Element_Group $group */
            preg_match_all("/F{(\w+)}/", $group->getGroupExpression(), $matchesF);
            $groupExpression = $matchesF[1][0];
            $shouldRender = false;
            if ($nextRow) {
                $nextGroupValue = carr::get($nextRow, $groupExpression);
                $groupValue = carr::get($row, $groupExpression);
                if ($nextGroupValue != $groupValue) {
                    $shouldRender = true;
                }
            } else {
                $shouldRender = true;
            }
            if (($rowIndex == $generator->getTotalRows() || $shouldRender) && ($group->hasGroupFooter())) {
                // cdbg::dd($group);
                $groupFooter = $group->getGroupFooterElement();
                $groupFooter->generate($generator, $processor);
            }

            if ($shouldRender) {
                //reset variable group here
                $generator->getDictionary()->resetVariableForGroup($group->getName());
            }
        }
    }

    public function getHeightForOverflow(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $height = null;
        if ($processor instanceof CReport_Generator_Processor_PdfProcessor) {
            $haveStretchOverflow = false;
            $maxHeight = 0;
            foreach ($this->children as $child) {
                if ($child instanceof CReport_Builder_Element_TextField) {
                    if ($child->isStretchWithOverflow()) {
                        $haveStretchOverflow = true;
                    }
                }
            }
            if ($haveStretchOverflow) {
                foreach ($this->children as $child) {
                    if ($child instanceof CReport_Builder_Element_TextField) {
                        $cellHeight = $child->getCellHeight($generator, $processor);

                        $originalHeight = $child->getHeight();
                        if ($cellHeight < $originalHeight) {
                            $cellHeight = $originalHeight;
                        }
                        // $cellHeight = 62;
                        if ($cellHeight > $maxHeight) {
                            $maxHeight = $cellHeight;
                        }
                    }
                }

                $height = $maxHeight;
            }
        }

        return $height;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $data = $generator->getData();
        $generator->setProcessingDetail(true);
        if (count($this->children) > 0) {
            $lastRow = null;
            foreach ($data as $rowIndex => $row) {
                $height = $this->getHeight();
                /** @var CReport_Builder_Row $row */
                $generator->setCurrentRow($row);
                $generator->setReportCount($rowIndex);
                $this->generaterGroupHeaders($generator, $processor, $rowIndex, $row, $lastRow);
                //calculate column height
                $heightForOverflow = $this->getHeightForOverflow($generator, $processor);
                if ($heightForOverflow !== null) {
                    $height = $heightForOverflow;
                }

                $processor->preventYOverflow($generator, $height);
                $generator->setColumnNumber(0);
                foreach ($this->children as $columnIndex => $child) {
                    $generator->setColumnNumber($columnIndex);
                    if ($child instanceof CReport_Builder_Element_TextField) {
                        $child->unforceHeight();
                        if ($child->isStretchWithOverflow() && $heightForOverflow) {
                            $child->forceHeight($heightForOverflow);
                        }
                    }
                    $child->generate($generator, $processor);
                }

                $processor->addY($height);
                $generator->variablesCalculation();
                $nextRow = carr::get($data, $rowIndex + 1);
                $this->generaterGroupFooters($generator, $processor, $rowIndex, $row, $nextRow);
                //if ($this->getSplitType() == CREPORT::SPLIT_TYPE_STRETCH || $this->getSplitType() == CREPORT::SPLIT_TYPE_PREVENT) {

                $lastRow = $row;
            }
        }
        $generator->setProcessingDetail(false);
    }
}

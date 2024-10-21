<?php

class CReport_Jasper_Element_PageHeader extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $band = carr::get($this->children, 0);
        $height = '';
        $print_expression_result = false;
        if ($band) {
            $height = (string) $band->xmlElement['height'];
            $printWhenExpression = (string) $band->xmlElement->printWhenExpression;

            if ($printWhenExpression != '') {
                $row = $report->getCurrentRow();

                $printWhenExpression = $report->getExpression($printWhenExpression, $row);
                eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
            } else {
                $print_expression_result = true;
            }
        }

        if ($print_expression_result == true) {
            parent::generate($report);
            if ($height) {
                $report->getInstructions()->addInstruction(CReport_Jasper_Instruction::TYPE_SET_Y_AXIS, [
                    'y_axis' => $height
                ]);
            }
        }
    }
}

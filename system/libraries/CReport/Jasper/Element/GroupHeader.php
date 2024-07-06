<?php

class CReport_Jasper_Element_GroupHeader extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $row = $report->getCurrentRow();
        foreach ($this->children as $child) {
            // se for objeto
            if (is_object($child)) {
                $print_expression_result = false;
                $printWhenExpression = (string) $child->xmlElement->printWhenExpression;
                if ($printWhenExpression != '') {
                    $printWhenExpression = $report->getExpression($printWhenExpression, $row);
                    //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
                    eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
                } else {
                    $print_expression_result = true;
                }
                if ($print_expression_result == true) {
                    if ($child->xmlElement['splitType'] == 'Stretch' || $child->xmlElement['splitType'] == 'Prevent') {
                        CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $child->xmlElement['height']]);
                    }
                    parent::generate($report);
                    CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $child->xmlElement['height']]);
                }
            }
        }
    }
}

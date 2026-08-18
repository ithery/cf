<?php

class CReport_Jasper_Element_Breaker extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $data = $this->xmlElement;
        $row = $report->getCurrentRow();
        $print_expression_result = false;
        $printWhenExpression = (string) $data->reportElement->printWhenExpression;
        if ($printWhenExpression != '') {
            $printWhenExpression = $report->getExpression($printWhenExpression, $row);
            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
        } else {
            $print_expression_result = true;
        }
        if ($print_expression_result == true) {
            CReport_Jasper_Instructions::addInstruction(['type' => 'break', 'printWhenExpression' => $printWhenExpression . '']);
        }
        parent::generate($report);
    }
}

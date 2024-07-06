<?php

class CReport_Jasper_Element_PageHeader extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $row = $report->getCurrentRow();
        $band = $this->children['0'];
        $height = (string) $band->objElement['height'];
        $print_expression_result = false;
        $printWhenExpression = (string) $band->objElement->printWhenExpression;

        if ($printWhenExpression != '') {
            $printWhenExpression = $report->getExpression($printWhenExpression, $row);
            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
        } else {
            $print_expression_result = true;
        }

        if ($print_expression_result == true) {
            parent::generate($report);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
        }
    }
}

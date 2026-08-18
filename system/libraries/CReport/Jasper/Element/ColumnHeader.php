<?php

class CReport_Jasper_Element_ColumnHeader extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        // cdbg::dd((string) $this->xmlElement->asXML());
        $row = $report->getCurrentRow();
        $print_expression_result = false;
        //var_dump((string)$child->xmlElement->printWhenExpression);
        //echo     (string)$child->xmlElement['printWhenExpression']."oi";
        $printWhenExpression = (string) $this->xmlElement->printWhenExpression;
        if ($printWhenExpression != '') {
            $printWhenExpression = $report->getExpression($printWhenExpression, $row);
            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
        } else {
            $print_expression_result = true;
        }
        if ($print_expression_result == true) {
            if ($this->children['0']->xmlElement->splitType == 'Stretch' || $this->children['0']->xmlElement->splitType == 'Prevent') {
                CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $this->children['0']->xmlElement['height']]);
            }
            parent::generate($report);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $this->children['0']->xmlElement['height']]);
        }
    }
}

<?php

class CReport_Jasper_Element_ColumnFooter extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $row = $report->getCurrentRow();
        if (!$row) {
            return;
        }
        foreach ($this->children as $child) {
            // se for objeto
            if (is_object($child)) {
                $print_expression_result = false;
                //var_dump((string)$child->objElement->printWhenExpression);
                //echo     (string)$child->objElement['printWhenExpression']."oi";
                $printWhenExpression = (string) $child->objElement->printWhenExpression;
                if ($printWhenExpression != '') {
                    $printWhenExpression = $report->getExpression($printWhenExpression, $row);
                    eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
                } else {
                    $print_expression_result = true;
                }
                if ($print_expression_result == true) {
                    if ($this->children['0']->objElement['splitType'] == 'Stretch' || $this->children['0']->objElement['splitType'] == 'Prevent') {
                        CReport_Jasper_Instructions::addInstruction(['type' => 'PreventY_axis', 'y_axis' => $this->children['0']->objElement['height']]);
                    }
                    parent::generate($report);
                    //var_dump($this->children['0']);
                    CReport_Jasper_Instructions::addInstruction(['type' => 'SetY_axis', 'y_axis' => $this->children['0']->objElement['height']]);
                }
            }
        }
    }
}

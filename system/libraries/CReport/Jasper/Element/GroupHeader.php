<?php

class CReport_Jasper_Element_GroupHeader extends CReport_Jasper_Element {
    public function generate($obj = null) {
        $row = is_array($obj) ? $obj[1] : [];
        $obj = is_array($obj) ? $obj[0] : $obj;
        foreach ($this->children as $child) {
            // se for objeto
            if (is_object($child)) {
                $print_expression_result = false;
                $printWhenExpression = (string) $child->objElement->printWhenExpression;
                if ($printWhenExpression != '') {
                    $printWhenExpression = $obj->getExpression($printWhenExpression, $row);
                    //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
                    eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
                } else {
                    $print_expression_result = true;
                }
                if ($print_expression_result == true) {
                    if ($child->objElement['splitType'] == 'Stretch' || $child->objElement['splitType'] == 'Prevent') {
                        CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $child->objElement['height']]);
                    }
                    parent::generate([$obj, $row]);
                    CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $child->objElement['height']]);
                }
            }
        }
    }
}

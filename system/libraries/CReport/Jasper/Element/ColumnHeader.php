<?php

class CReport_Jasper_Element_ColumnHeader extends CReport_Jasper_Element {
    public function generate($obj = null) {
        $row = is_array($obj) ? $obj[1] : [];
        $obj = is_array($obj) ? $obj[0] : $obj;
        $print_expression_result = false;
        //var_dump((string)$child->objElement->printWhenExpression);
        //echo     (string)$child->objElement['printWhenExpression']."oi";
        $printWhenExpression = (string) $this->objElement->printWhenExpression;
        if ($printWhenExpression != '') {
            $printWhenExpression = $obj->getExpression($printWhenExpression, $row);
            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
        } else {
            $print_expression_result = true;
        }
        if ($print_expression_result == true) {
            if ($this->children['0']->objElement->splitType == 'Stretch' || $this->children['0']->objElement->splitType == 'Prevent') {
                CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $this->children['0']->objElement['height']]);
            }
            parent::generate($obj);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $this->children['0']->objElement['height']]);
        }
    }
}

<?php

class CReport_Jasper_Element_PageHeader extends CReport_Jasper_Element {
    public function generate($obj = null) {
        $row = (array) $obj->rowData;
        $data = $this->objElement;
        $obj = is_array($obj) ? $obj[0] : $obj;
        $band = $this->children['0'];
        $height = (string) $band->objElement['height'];
        $print_expression_result = false;
        $printWhenExpression = (string) $band->objElement->printWhenExpression;

        if ($printWhenExpression != '') {
            $printWhenExpression = $obj->get_expression($printWhenExpression, $row);
            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
        } else {
            $print_expression_result = true;
        }

        if ($print_expression_result == true) {
            parent::generate([$obj, $row]);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
        }
    }
}

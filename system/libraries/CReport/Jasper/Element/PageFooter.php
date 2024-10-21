<?php

class CReport_Jasper_Element_PageFooter extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {

        // $arrayVariable = ($obj->arrayVariable) ? $obj->arrayVariable : array();
        // $recordObject = array_key_exists('recordObj', $arrayVariable) ? $arrayVariable['recordObj']['initialValue'] : "stdClass";
        // $rowIndex = 0;

        $height = (string) $this->children['0']->xmlElement['height'];
        CReport_Jasper_Instructions::addInstruction(['type' => 'resetYAxis']);
        CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => ($report->arrayPageSetting['pageHeight'] - $report->arrayPageSetting['topMargin'] - $this->children['0']->height - $report->arrayPageSetting['bottomMargin'])]);
        CReport_Jasper_Instructions::$processingPageFooter = true;
        parent::generate($report);
        CReport_Jasper_Instructions::$processingPageFooter = false;

        CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
    }
}

<?php

class CReport_Jasper_Element_Summary extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $height = (string) $this->children['0']->xmlElement['height'];
        if ($this->children['0']->splitType == 'Stretch' || $this->children['0']->splitType == 'Prevent') {
            CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $height]);
        }
        parent::generate($report);
        if ($this->children['0']->splitType == 'Stretch' || $this->children['0']->splitType == 'Prevent') {
            CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
        }
    }
}

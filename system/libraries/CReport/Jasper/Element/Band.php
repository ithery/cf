<?php

class CReport_Jasper_Element_Band extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $height = $this->getProperty('height');
        CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $height]);
        parent::generate($report);
    }
}

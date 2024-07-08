<?php

class CReport_Jasper_Element_Band extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $height = $this->getProperty('height');
        if ($height) {
            $report->getInstructions()->addInstruction(CReport_Jasper_Instruction::TYPE_PREVENT_Y_AXIS, [
                'y_axis' => $height
            ]);
        }

        parent::generate($report);
    }
}

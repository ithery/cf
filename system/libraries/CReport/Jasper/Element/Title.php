<?php

class CReport_Jasper_Element_Title extends CReport_Jasper_Element {
    public function generate($obj = null) {
        $data = $obj->data;

        $arrayVariable = ($obj->arrayVariable) ? $obj->arrayVariable : [];
        $recordObject = array_key_exists('recordObj', $arrayVariable) ? $arrayVariable['recordObj']['initialValue'] : 'stdClass';
        //$row = (is_array($dbData) || $dbData instanceof \ArrayAccess) ? (isset($dbData[0]) ? $dbData[0] : []) : $obj->rowData;

        if (!$data) {
            $data = c::collect();
        }

        foreach ($this->children as $child) {
            // se for objeto
            if (is_object($child)) {
                $height = (string) $this->children['0']->objElement['height'];
                parent::generate([$obj, $data]);
                CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
            }
        }
    }
}

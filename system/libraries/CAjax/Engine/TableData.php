<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAjax_Engine_TableData extends CAjax_Engine {
    use CTrait_Element_Property_TableData;

    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();
        $json = carr::get($data, 'json');
        $this->populateTableData($data);
    }
}

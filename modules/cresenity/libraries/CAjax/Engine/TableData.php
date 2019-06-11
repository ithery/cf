<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2019, 2:57:22 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_TableData extends CAjax_Engine {

    use CTrait_Element_Property_TableData;

    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();
        $json = carr::get($data, 'json');
        $this->populateTableData($data);
  
    }

}

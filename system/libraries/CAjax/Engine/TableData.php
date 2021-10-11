<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 3, 2019, 2:57:22 AM
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

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 2:58:18 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable_Processor_Query extends CAjax_Engine_DataTable_Processor {

    use CAjax_Engine_DataTable_Trait_ProcessorTrait;
    use CAjax_Engine_DataTable_Trait_ProcessorQueryTrait;

    public function process() {

        $db = $this->db();

        $request = $this->input;

        $qProcess = $this->getFullQuery($withPagination = true);
        $resultQ = $db->query($qProcess);

        $data = $resultQ->result(false);


        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $this->getTotalRecord(),
            "iTotalDisplayRecords" => $this->getTotalFilteredRecord(),
            "aaData" => $this->populateAAData($data, $this->table(), $request, $js),
        );



        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}

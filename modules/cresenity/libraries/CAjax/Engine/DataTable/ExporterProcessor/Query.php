<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CAjax_Engine_DataTable_ExporterProcessor_Query extends CAjax_Engine_DataTable_Processor {

    use CAjax_Engine_DataTable_Trait_ProcessorTrait;
    use CAjax_Engine_DataTable_Trait_ProcessorQueryTrait;

    public function process() {

        $db = $this->db();

        $response='';
        $request = $this->input;


        $qProcess = $this->getFullQuery($withPagination = false);
        $response=$qProcess;

        return $response;
    }

}

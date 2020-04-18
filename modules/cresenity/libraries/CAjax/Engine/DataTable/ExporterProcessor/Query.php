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

        $response = '';
        $request = $this->input;




        $action = $this->getData('exporter.action', CExporter::ACTION_DOWNLOAD);
        $queued = $this->getData('exporter.queued', false);
        $writerType = $this->getData('exporter.writerType', CExporter::XLS);

        $filename = $this->getData('exporter.filename', CExporter::randomFilename($writerType));

        //$qProcess = $this->getFullQuery($withPagination = false);
        //$result = $db->query($qProcess)->result(false);
        //$exportable = c::collect($result);
        
        $exportable = $this->table()->toExportable();
        if ($action == CExporter::ACTION_STORE) {
            $exportOptions = [];
            $exportOptions['writerType'] = $writerType;
            $exportOptions['queued'] = $queued;
            CExporter::store($exportable, $filename, $exportOptions);
        } else {
            CExporter::download($exportable, $filename, $writerType);
        }
        return $response;
    }

}

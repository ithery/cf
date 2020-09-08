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
        $progress = $this->getData('progress', false);

        $filename = $this->getData('exporter.filename', CExporter::randomFilename($writerType));
        $disk = $this->getData('exporter.disk', 'local-temp');





        $response = '';
        if ($progress) {
            $args = $this->engine->getArgs();
            $fileId = carr::first($args);
            $exportable = new CExporter_Exportable_DataTableTemp($fileId);
            $exportOptions = [];
            $exportOptions['writerType'] = $writerType;
            $exportOptions['queued'] = $queued;
            $exportOptions['diskName']=$disk;


            $storeResult = CExporter::store($exportable, $filename, $exportOptions);

            if ($queued) {
                $queueConnection = $this->getData('exporter.queueConnection', false);

                $queueParams = [];
                $queueParams['downloadId'] = $fileId;
                $storeResult = $storeResult->chain([
                    new CElement_Component_DataTable_TaskQueue_AfterExportProgress($queueParams)
                ]);
                if ($queueConnection) {
                    $storeResult->allOnConnection($queueConnection);
                }
            }

            $ajaxMethod = CAjax::createMethod();
            $ajaxMethod->setType('DataTableExporterProgress');
            $ajaxMethod->setData('downloadId', $fileId);
            $progressUrl = $ajaxMethod->makeUrl();


            $responseData = [
                'downloadId' => $fileId,
                'progressUrl' => $progressUrl,
            ];

            $response = json_encode([
                'errCode' => 0,
                'errMessage' => '',
                'data' => $responseData,
            ]);
        } else {
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
        }


        return $response;
    }

}

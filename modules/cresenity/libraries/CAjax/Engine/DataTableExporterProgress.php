<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class CAjax_Engine_DataTableExporterProgress extends CAjax_Engine {


    public function execute() {



        $data = $this->ajaxMethod->getData();

        $downloadId = carr::get($data, 'downloadId');
        $data = CAjax::getData($downloadId);
        
        $progressValue = carr::get($data,'data.progressValue');
        $progressMax = carr::get($data,'data.progressMax');
        $state = carr::get($data,'data.state');
        $exporter = carr::get($data,'data.exporter');
        $fileUrl = carr::get($data,'data.fileUrl');
        
        $responseArray = [
            'errCode'=>0,
            'errMessage'=>'',
            'data'=>[
                'progressValue'=>$progressValue,
                'progressMax'=>$progressMax,
                'state'=>$state,
                'exporter'=>$exporter,
                'fileUrl'=>$fileUrl,
            ]
        ];
       
        $response = json_encode($responseArray);
        return $response;
    }

}

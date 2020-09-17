<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_DataTable_TaskQueue_AfterExportProgress extends CQueue_AbstractTask {
    
    protected $params;

    public function __construct($params) {
        $this->params = $params;
    }
    
    public function execute() {
        $params = $this->params;
      
        $downloadId = carr::get($params, 'downloadId');

        $data = CAjax::getData($downloadId);
        
        //check file exists
        
        $filename = carr::get($data,'data.exporter.filename');
        
        $disk = CStorage::instance()->disk(carr::get($data,'data.exporter.disk'));
        $isReady = $disk->exists($filename);
        
        if ($isReady) {
            
            $data['data']['progressValue']=carr::get($data,'data.progressMax');
            $data['data']['state']='DONE';
            CAjax::setData($downloadId,$data);
        
        }
      
        
        $this->logDaemon("AfterExportProgress | downloadId:" . $downloadId);
    }
}

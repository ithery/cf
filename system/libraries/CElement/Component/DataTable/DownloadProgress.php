<?php

/**
 * Description of DownloadProgress.
 *
 * @author ekosantoso
 *
 * @deprecated since 1.3 dont use this
 */
class CElement_Component_DataTable_DownloadProgress {
    const TEMP_DOWNLOAD_PROGRESS_FOLDER = 'download-progress';

    protected $downloadId;

    protected $ajaxUrl;

    public function __construct() {
        // $fullFilename = CTemporary::put(self::TEMP_DOWNLOAD_PROGRESS_FOLDER, $this->jsonInfo(), $this->downloadId);
        // $this->ajaxUrl = CTemporary::getUrl(self::TEMP_DOWNLOAD_PROGRESS_FOLDER, $this->downloadId);

        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType('DownloadProgress');
        // $ajaxMethod->setData('columns', $columns);
        $ajaxUrl = $ajaxMethod->makeUrl();
        $this->ajaxUrl = $ajaxUrl;

        $ajaxMethod = carr::last(explode('/', $ajaxUrl));
        $this->downloadId = $ajaxMethod;
    }

    public function jsonResponse() {
        return json_encode([
            'errCode' => 0,
            'errMessage' => '',
            'data' => $this->downloadInfo()
        ]);
    }

    public function jsonInfo() {
        return json_encode($this->downloadInfo());
    }

    public function downloadInfo() {
        $data = [];
        $data['downloadId'] = $this->downloadId;
        $data['ajaxUrl'] = $this->ajaxUrl;

        return $data;
    }

    public function exportQueue($exportable, $filePath, $disk = null, $writerType = null, $diskOptions = []) {
        $queueParams = $this->downloadInfo();

        $taskQueue = CExporter::queue($exportable, $filePath, $disk, $writerType, $diskOptions)->chain([
            //new CApp_DownloadProgress_TaskQueue_AfterExportQueue($queueParams)
        ]);

        return $taskQueue;
    }
}

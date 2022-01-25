<?php

class CExporter_Exportable_DataTableTemp extends CExporter_Exportable_DataTable {
    protected $file;

    protected $downloadId;

    public function __construct($file) {
        $this->file = $file;
        $data = CAjax::getData($this->file);

        $table = unserialize(carr::get($data, 'data.table'));
        $this->table = $table;
        $this->columnFormats = [];
    }

    public function setDownloadId($downloadId) {
        $this->downloadId = $downloadId;

        return $this;
    }

    public function getDownloadId() {
        return $this->downloadId;
    }
}

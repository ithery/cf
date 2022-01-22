<?php

class CExporter_Exportable_DataTableTemp extends CExporter_Exportable implements CExporter_Concern_FromDataTable, CExporter_Concern_WithHeadings, CExporter_Concern_WithMapping {
    protected $file;

    protected $downloadId;

    public function __construct($file) {
        $this->file = $file;
    }

    protected function table() {
        $data = CAjax::getData($this->file);

        $table = unserialize(carr::get($data, 'data.table'));

        return new CExporter_Exportable_DataTable($table);
    }

    public function dataTable() {
        return $this->table()->dataTable();
    }

    public function map($data) {
        return $this->table()->map($data);
    }

    public function headings() {
        return $this->table()->headings();
    }

    public function setDownloadId($downloadId) {
        $this->downloadId = $downloadId;

        return $this;
    }

    public function getDownloadId() {
        return $this->downloadId;
    }
}

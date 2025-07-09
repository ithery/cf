<?php

class CAjax_FileUpload {
    protected $fileId;

    protected $fileName;

    protected $type;

    public function __construct($type, $fileId, $fileName) {
        $this->type = $type;
        $this->fileId = $fileId;
        $this->fileName = $fileName;
    }
}

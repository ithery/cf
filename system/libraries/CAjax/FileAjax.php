<?php

class CAjax_FileAjax {
    const TYPE_FILE = 'file';

    const TYPE_IMAGE = 'image';

    protected $fileId;

    protected $fileName;

    protected $type;

    public function __construct($fileId) {
        $this->fileId = $fileId;
        $filenameWithoutExtension = pathinfo($fileId, PATHINFO_FILENAME);
        $lastChar = substr($filenameWithoutExtension, -1);
        $this->type = $lastChar == 'i' ? self::TYPE_IMAGE : self::TYPE_FILE;
    }

    public function getType() {
        return $this->type;
    }

    public function haveInfo() {
    }
}

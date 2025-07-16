<?php

class CAjax_FileAjax {
    const TYPE_FILE = 'file';

    const TYPE_IMAGE = 'image';

    protected $fileId;

    protected $fileName;

    protected $type;

    protected $extension;

    protected $path;

    public function __construct($fileId) {
        $this->fileId = $fileId;
        $filenameWithoutExtension = pathinfo($fileId, PATHINFO_FILENAME);
        $this->extension = pathinfo($fileId, PATHINFO_EXTENSION);
        $lastChar = substr($filenameWithoutExtension, -1);
        $this->type = $lastChar == 'i' ? self::TYPE_IMAGE : self::TYPE_FILE;
        $this->path = CTemporary::getPath($this->getTemporaryFolderName(), $fileId);
    }
    protected function getTemporaryFolderName() {
        return CF::config('temporary.upload.' . $this->getType(), $this->getType() == CAjax_FileAjax::TYPE_IMAGE ? 'imgupload' : 'fileupload');
    }

    protected function getInfoTemporaryFolderName() {
        return CF::config('temporary.upload.' . $this->getType() . '_info', $this->getType() == CAjax_FileAjax::TYPE_IMAGE ? 'imguploadinfo' : 'fileuploadinfo');
    }

    /**
     * Get the type of the file.
     *
     * @return string The type of the file, either 'file' or 'image'.
     */
    public function getType() {
        return $this->type;
    }
    protected function getInfoPath() {
        return CTemporary::getPath($this->getInfoTemporaryFolderName(), $this->fileId);
    }

    public function haveInfo() {
        $infoPath = $this->getInfoPath();
        return $this->getDisk()->exists($infoPath);
    }

    public function getUrl() {
        return CTemporary::getPublicUrl($this->getTemporaryFolderName(), $this->fileId);
    }

    /**
     * @return array The information about the file.
     */
    public function getInfo() {
        $path = $this->getInfoPath();
        $info = $this->getDisk()->get($path);

        return json_decode($info, true);
    }
    /**
     * Get the disk used for storing temporary files.
     *
     * @return CStorage_Adapter The storage adapter for the public temporary disk.
     */
    protected function getDisk() {
        return CTemporary::publicDisk();
    }
}

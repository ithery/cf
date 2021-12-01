<?php

class CExporter_TaskQueue_StoreQueuedExport extends CQueue_AbstractTask {
    /**
     * @var string
     */
    private $filePath;

    /**
     * @var null|string
     */
    private $disk;

    /**
     * @var CExporter_File_TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var array|string
     */
    private $diskOptions;

    /**
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $filePath
     * @param null|string                  $disk
     * @param array|string                 $diskOptions
     */
    public function __construct(CExporter_File_TemporaryFile $temporaryFile, $filePath, $disk = null, $diskOptions = []) {
        $this->disk = $disk;
        $this->filePath = $filePath;
        $this->temporaryFile = $temporaryFile;
        $this->diskOptions = $diskOptions;
    }

    public function execute() {
        $storage = CExporter_Storage::instance();
        CDaemon::log('Try to copy ' . $this->temporaryFile->getLocalPath() . ' to ' . $this->filePath . ', disk:' . $this->disk);
        $storage->disk($this->disk, $this->diskOptions)->copy(
            $this->temporaryFile,
            $this->filePath
        );

        CDaemon::log('Try to delete temporary file');

        $this->temporaryFile->delete();

        CDaemon::log('Dispatch Event CExporter_Event_AfterExport');
        CExporter::dispatcher()->dispatch(new CExporter_Event_AfterExport($this->filePath));
    }
}

<?php

class CExporter_TaskQueue_AppendViewToSheet implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_InteractsWithQueue;

    /**
     * @var CExporter_File_TemporaryFile
     */
    public $temporaryFile;

    /**
     * @var string
     */
    public $writerType;

    /**
     * @var int
     */
    public $sheetIndex;

    /**
     * @var CExporter_Concern_FromView
     */
    public $sheetExport;

    /**
     * @param CExporter_Concern_FromView   $sheetExport
     * @param CExporter_File_TemporaryFile $temporaryFile
     * @param string                       $writerType
     * @param int                          $sheetIndex
     */
    public function __construct(
        CExporter_Concern_FromView $sheetExport,
        CExporter_File_TemporaryFile $temporaryFile,
        $writerType,
        $sheetIndex
    ) {
        $this->sheetExport = $sheetExport;
        $this->temporaryFile = $temporaryFile;
        $this->writerType = $writerType;
        $this->sheetIndex = $sheetIndex;
    }

    /**
     * Get the middleware the job should be dispatched through.
     *
     * @return array
     */
    public function middleware() {
        return (method_exists($this->sheetExport, 'middleware')) ? $this->sheetExport->middleware() : [];
    }

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function handle() {
        $writer = CExporter::writer();
        (new CExporter_TaskQueue_Middleware_LocalizeJob($this->sheetExport))->handle($this, function () use ($writer) {
            $writer = $writer->reopen($this->temporaryFile, $this->writerType);

            $sheet = $writer->getSheetByIndex($this->sheetIndex);

            $sheet->fromView($this->sheetExport, $this->sheetIndex);

            $writer->write($this->sheetExport, $this->temporaryFile, $this->writerType);
        });
    }
}

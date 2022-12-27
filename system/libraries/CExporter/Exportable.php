<?php

abstract class CExporter_Exportable implements CExporter_Concern_WithEvents {
    protected $beforeSheets = [];

    protected $afterSheets = [];

    protected $orientation = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT;

    protected function beforeSheet($callback) {
        $this->beforeSheets[] = $callback;
    }

    protected function afterSheet($callback) {
        $this->afterSheets[] = $callback;
    }

    /**
     * @return array
     */
    public function registerEvents() {
        return [
            CExporter_Event_AfterSheet::class => [$this, 'handleAfterSheet'],
            CExporter_Event_BeforeSheet::class => [$this, 'handleBeforeSheet'],
        ];
    }

    public function getOrientation() {
        return $this->orientation;
    }

    public function setOrientation($orientation) {
        $this->orientation = $orientation;

        return $this;
    }

    public function handleBeforeSheet(CExporter_Event_BeforeSheet $event) {
        $event->sheet
            ->getPageSetup()
            ->setOrientation($this->orientation);
        foreach ($this->beforeSheets as $c) {
            c::call($c, [$event]);
        }
    }

    public function handleAfterSheet(CExporter_Event_AfterSheet $event) {
        foreach ($this->afterSheets as $c) {
            c::call($c, [$event]);
        }
    }

    public function download($filename = null, $writerType = null, array $headers = []) {
        if ($filename == null) {
            $filename = CExporter::randomFilename();
        }
        // @phpstan-ignore-next-line
        return CExporter::toDownloadResponse($this, $filename, $writerType, $headers);
    }
}

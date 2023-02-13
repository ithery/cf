<?php

use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class CExporter_PhpSpreadsheet_SnappyPdf extends Pdf {
    /**
     * Gets the implementation of external PDF library that should be used.
     *
     * @return CExporter_Snappy_PdfWrapper implementation
     */
    protected function createExternalWriterInstance() {
        return CExporter_Snappy::pdf();
    }

    /**
     * Save Spreadsheet to file.
     *
     * @param resource|string $filename
     */
    public function save($filename, int $flags = 0): void {
        $fileHandle = parent::prepareForSave($filename);

        //  Default PDF paper size
        $paperSize = 'LETTER'; //    Letter    (8.5 in. by 11 in.)

        //  Check for paper size and page orientation
        if ($this->getSheetIndex() === null) {
            $orientation = ($this->spreadsheet->getSheet(0)->getPageSetup()->getOrientation()
                == PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->spreadsheet->getSheet(0)->getPageSetup()->getPaperSize();
        } else {
            $orientation = ($this->spreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation()
                == PageSetup::ORIENTATION_LANDSCAPE) ? 'L' : 'P';
            $printPaperSize = $this->spreadsheet->getSheet($this->getSheetIndex())->getPageSetup()->getPaperSize();
        }

        $orientation = ($orientation == 'L') ? 'landscape' : 'portrait';

        //  Override Page Orientation
        if ($this->getOrientation() !== null) {
            $orientation = ($this->getOrientation() == PageSetup::ORIENTATION_DEFAULT)
                ? PageSetup::ORIENTATION_PORTRAIT
                : $this->getOrientation();
        }
        //  Override Paper Size
        if ($this->getPaperSize() !== null) {
            $printPaperSize = $this->getPaperSize();
        }

        if (isset(self::$paperSizes[$printPaperSize])) {
            $paperSize = self::$paperSizes[$printPaperSize];
        }

        //  Create PDF
        $pdf = $this->createExternalWriterInstance();

        $pdf->setOptions(
            [
                'page-size' => strtoupper($paperSize),
                'orientation' => $orientation,
                'margin-left' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getLeft()),
                'margin-right' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getRight()),
                'margin-top' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getTop()),
                'margin-bottom' => $this->inchesToMm(
                    $this->spreadsheet->getActiveSheet()->getPageMargins()->getBottom()
                ),
                'title' => $this->spreadsheet->getProperties()->getTitle(),
            ]
        );

        //  Write to file
        fwrite($fileHandle, $pdf->getOutputFromHtml($this->generateHTMLAll()));

        parent::restoreStateAfterSave();
    }

    /**
     * Convert inches to mm.
     *
     * @param float $inches
     *
     * @return float
     */
    private function inchesToMm($inches) {
        return $inches * 25.4;
    }
}

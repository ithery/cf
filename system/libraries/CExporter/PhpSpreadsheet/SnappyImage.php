<?php

use PhpOffice\PhpSpreadsheet\Writer\Pdf;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

class CExporter_PhpSpreadsheet_SnappyImage extends Pdf {
    /**
     * Dimensions of paper sizes in points.
     *
     * @var array
     */
    public static $PAPER_SIZES = [
        '4a0' => [4767.87, 6740.79],
        '2a0' => [3370.39, 4767.87],
        'a0' => [2383.94, 3370.39],
        'a1' => [1683.78, 2383.94],
        'a2' => [1190.55, 1683.78],
        'a3' => [841.89, 1190.55],
        'a4' => [595.28, 841.89],
        'a5' => [419.53, 595.28],
        'a6' => [297.64, 419.53],
        'a7' => [209.76, 297.64],
        'a8' => [147.40, 209.76],
        'a9' => [104.88, 147.40],
        'a10' => [73.70, 104.88],
        'b0' => [2834.65, 4008.19],
        'b1' => [2004.09, 2834.65],
        'b2' => [1417.32, 2004.09],
        'b3' => [1000.63, 1417.32],
        'b4' => [708.66, 1000.63],
        'b5' => [498.90, 708.66],
        'b6' => [354.33, 498.90],
        'b7' => [249.45, 354.33],
        'b8' => [175.75, 249.45],
        'b9' => [124.72, 175.75],
        'b10' => [87.87, 124.72],
        'c0' => [2599.37, 3676.54],
        'c1' => [1836.85, 2599.37],
        'c2' => [1298.27, 1836.85],
        'c3' => [918.43, 1298.27],
        'c4' => [649.13, 918.43],
        'c5' => [459.21, 649.13],
        'c6' => [323.15, 459.21],
        'c7' => [229.61, 323.15],
        'c8' => [161.57, 229.61],
        'c9' => [113.39, 161.57],
        'c10' => [79.37, 113.39],
        'ra0' => [2437.80, 3458.27],
        'ra1' => [1729.13, 2437.80],
        'ra2' => [1218.90, 1729.13],
        'ra3' => [864.57, 1218.90],
        'ra4' => [609.45, 864.57],
        'sra0' => [2551.18, 3628.35],
        'sra1' => [1814.17, 2551.18],
        'sra2' => [1275.59, 1814.17],
        'sra3' => [907.09, 1275.59],
        'sra4' => [637.80, 907.09],
        'letter' => [612.00, 792.00],
        'half-letter' => [396.00, 612.00],
        'legal' => [612.00, 1008.00],
        'ledger' => [1224.00, 792.00],
        'tabloid' => [792.00, 1224.00],
        'executive' => [521.86, 756.00],
        'folio' => [612.00, 936.00],
        'commercial #10 envelope' => [684.00, 297.00],
        'catalog #10 1/2 envelope' => [648.00, 864.00],
        '8.5x11' => [612.00, 792.00],
        '8.5x14' => [612.00, 1008.00],
        '11x17' => [792.00, 1224.00],
    ];

    /**
     * Gets the implementation of external PDF library that should be used.
     *
     * @return CExporter_Snappy_PdfWrapper implementation
     */
    protected function createExternalWriterInstance() {
        return CExporter_Snappy::image();
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
        $image = $this->createExternalWriterInstance();
        $paperSize = $this->getPaperSize();
        if (is_array($paperSize)) {
            $size = array_map('floatval', $paperSize);
        } else {
            $paperSize = strtolower($paperSize);
            $size = self::$PAPER_SIZES[$paperSize] ?? self::$PAPER_SIZES['letter'];
        }
        $width = $size[0];
        $height = $size[1];
        if (cstr::tolower($orientation) == 'landscape') {
            $temp = $width;
            $width = $height;
            $height = $temp;
        }
        $image->setOptions(
            [
                //'margin-left' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getLeft()),
                //'margin-right' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getRight()),
                //'margin-top' => $this->inchesToMm($this->spreadsheet->getActiveSheet()->getPageMargins()->getTop()),
                // 'margin-bottom' => $this->inchesToMm(
                //     $this->spreadsheet->getActiveSheet()->getPageMargins()->getBottom()
                // ),
                //'title' => $this->spreadsheet->getProperties()->getTitle(),
            ]
        );
        //  Write to file
        fwrite($fileHandle, $image->getOutputFromHtml($this->generateHTMLAll()));

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

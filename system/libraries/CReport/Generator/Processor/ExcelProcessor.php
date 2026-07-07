<?php

/**
 * Excel processor for CReport.
 *
 * Instead of drawing directly, every element is recorded as a draw operation with
 * absolute coordinates. The operations are then converted into a CReport_Excel_Grid
 * (JasperReports-like layout mapping) which can be written to a spreadsheet or
 * exported through CExporter (see CReport_Excel_Export).
 *
 * Pagination is ignored, the whole report flows on one continuous sheet, the same
 * way JasperReports exports xlsx with isIgnorePagination.
 */
class CReport_Generator_Processor_ExcelProcessor extends CReport_Generator_ProcessorAbstract {
    /**
     * Recorded draw operations.
     *
     * @var array
     */
    protected $ops = [];

    /**
     * @var null|CReport_Excel_Grid
     */
    protected $grid;

    /**
     * @param CReport_Builder_Report $report
     */
    public function __construct(CReport_Builder_Report $report) {
        parent::__construct($report);
        $this->currentY = 0;
    }

    /**
     * @param int $page
     *
     * @return void
     */
    public function setPage($page) {
        //pagination is ignored on excel export
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function cell(array $options) {
        $font = carr::get($options, 'font');
        $box = carr::get($options, 'box');
        $pattern = carr::get($options, 'pattern');
        $rawText = carr::get($options, 'rawText');
        $text = carr::get($options, 'text');

        $numeric = false;
        $value = $text;
        $format = null;
        if ($pattern && $rawText !== null && is_numeric($rawText)) {
            //keep the raw numeric value and let excel format it, like the jasper detected cell type
            $numeric = true;
            $value = $rawText + 0;
            $format = $pattern;
        }

        $style = [
            'align' => $this->mapAlignment(carr::get($options, 'textAlignment')),
            'valign' => $this->mapVerticalAlignment(carr::get($options, 'verticalAlignment')),
        ];
        if ($font instanceof CReport_Builder_Object_Font) {
            $style['fontName'] = $font->getFontName();
            $style['fontSize'] = $font->getFontSize();
            $style['bold'] = $font->isBold();
            $style['italic'] = $font->isItalic();
            $style['underline'] = $font->isUnderline();
            $style['strike'] = $font->isStrikeThrough();
        }
        $foregroundColor = carr::get($options, 'foregroundColor');
        if ($foregroundColor) {
            $style['color'] = $this->toArgb($foregroundColor);
        }
        $backgroundColor = carr::get($options, 'backgroundColor');
        if ($backgroundColor && carr::get($options, 'mode') != CReport::MODEL_TRANSPARENT) {
            $style['fill'] = $this->toArgb($backgroundColor);
        }
        if ($box instanceof CReport_Builder_Object_Box) {
            $borders = [];
            $penMap = [
                'top' => $box->getTopPen(),
                'bottom' => $box->getBottomPen(),
                'left' => $box->getLeftPen(),
                'right' => $box->getRightPen(),
            ];
            $allPen = $box->getPen();
            foreach ($penMap as $side => $pen) {
                $pen = $pen ?: $allPen;
                if ($pen instanceof CReport_Builder_Object_Pen && $pen->getLineWidth() > 0) {
                    $borders[$side] = $this->mapPen($pen);
                }
            }
            if (count($borders)) {
                $style['borders'] = $borders;
            }
        }

        $this->ops[] = [
            'type' => 'cell',
            'x' => (float) carr::get($options, 'x'),
            'y' => $this->currentY + (float) carr::get($options, 'y'),
            'width' => (float) carr::get($options, 'width'),
            'height' => (float) carr::get($options, 'height'),
            'text' => $value,
            'numeric' => $numeric,
            'format' => $format,
            'style' => $style,
        ];
        $this->grid = null;
    }

    /**
     * @param array $options
     *
     * @return float
     */
    public function cellHeight(array $options) {
        return (float) carr::get($options, 'height', 0);
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function image(array $options) {
        //image is not supported on excel export yet
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function line(array $options) {
        $pen = carr::get($options, 'pen');
        $this->ops[] = [
            'type' => 'line',
            'x' => (float) carr::get($options, 'x'),
            'y' => $this->currentY + (float) carr::get($options, 'y'),
            'width' => (float) carr::get($options, 'width'),
            'height' => (float) carr::get($options, 'height'),
            'border' => $this->mapPen($pen),
        ];
        $this->grid = null;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function rectangle(array $options) {
        $pen = carr::get($options, 'pen');
        $this->ops[] = [
            'type' => 'rect',
            'x' => (float) carr::get($options, 'x'),
            'y' => $this->currentY + (float) carr::get($options, 'y'),
            'width' => (float) carr::get($options, 'width'),
            'height' => (float) carr::get($options, 'height'),
            'border' => $this->mapPen($pen),
        ];
        $this->grid = null;
    }

    /**
     * @param float $height
     *
     * @return float
     */
    public function addY($height) {
        $this->currentY += $height;

        return $this->currentY;
    }

    /**
     * @param CReport_Generator $generator
     * @param float             $height
     *
     * @return float
     */
    public function preventYOverflow(CReport_Generator $generator, $height) {
        //no page break on excel export, the sheet flows continuously
        return $this->currentY;
    }

    /**
     * @param float $y
     *
     * @return float
     */
    public function setY($y) {
        return $this->currentY = $y;
    }

    /**
     * @return float
     */
    public function resetY() {
        return $this->currentY = 0;
    }

    /**
     * @return float
     */
    public function getY() {
        return $this->currentY;
    }

    /**
     * @return void
     */
    public function resetTextColor() {
    }

    /**
     * @param string $content
     *
     * @return void
     */
    public function raw($content) {
        //raw pdf content is not applicable on excel export
    }

    /**
     * @param string $fontName
     * @param string $fontPath
     *
     * @return void
     */
    public function addFont($fontName, $fontPath) {
    }

    /**
     * Get the grid built from the recorded operations.
     *
     * @return CReport_Excel_Grid
     */
    public function getGrid() {
        if ($this->grid === null) {
            $this->grid = CReport_Excel_Grid::fromOps($this->ops);
        }

        return $this->grid;
    }

    /**
     * Build the spreadsheet from the recorded operations.
     *
     * @return CReport_Adapter_Excel_PhpSpreadsheet
     */
    public function getOutput() {
        $spreadsheet = new CReport_Adapter_Excel_PhpSpreadsheet();
        $writer = new CReport_Excel_GridWriter($this->getGrid());
        $writer->write($spreadsheet->getActiveSheet());

        return $spreadsheet;
    }

    /**
     * @param null|string $alignment CReport::TEXT_ALIGNMENT_* value
     *
     * @return string PhpSpreadsheet horizontal alignment
     */
    protected function mapAlignment($alignment) {
        $map = [
            CReport::TEXT_ALIGNMENT_LEFT => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            CReport::TEXT_ALIGNMENT_CENTER => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            CReport::TEXT_ALIGNMENT_RIGHT => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            CReport::TEXT_ALIGNMENT_JUSTIFIED => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_JUSTIFY,
        ];

        return carr::get($map, $alignment, \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
    }

    /**
     * @param null|string $alignment CReport::VERTICAL_ALIGNMENT_* value
     *
     * @return string PhpSpreadsheet vertical alignment
     */
    protected function mapVerticalAlignment($alignment) {
        $map = [
            CReport::VERTICAL_ALIGNMENT_TOP => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            CReport::VERTICAL_ALIGNMENT_MIDDLE => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            CReport::VERTICAL_ALIGNMENT_BOTTOM => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
        ];

        return carr::get($map, $alignment, \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
    }

    /**
     * Convert a pen into a PhpSpreadsheet border style array.
     *
     * @param null|CReport_Builder_Object_Pen $pen
     *
     * @return array
     */
    protected function mapPen($pen) {
        if (!$pen instanceof CReport_Builder_Object_Pen) {
            return ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['argb' => 'FF000000']];
        }
        $width = $pen->getLineWidth();
        $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN;
        if ($pen->getLineStyle() == CReport::LINE_STYLE_DASHED) {
            $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DASHED;
        } elseif ($pen->getLineStyle() == CReport::LINE_STYLE_DOTTED) {
            $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED;
        } elseif ($pen->getLineStyle() == CReport::LINE_STYLE_DOUBLE) {
            $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE;
        } elseif ($width >= 2) {
            $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK;
        } elseif ($width >= 1) {
            $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM;
        } elseif ($width < 0.5) {
            $style = \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR;
        }

        return ['borderStyle' => $style, 'color' => ['argb' => $this->toArgb($pen->getLineColor())]];
    }

    /**
     * Convert a #RRGGBB color into ARGB.
     *
     * @param string $color
     *
     * @return string
     */
    protected function toArgb($color) {
        $color = ltrim((string) $color, '#');
        if (strlen($color) == 6) {
            return 'FF' . strtoupper($color);
        }

        return strtoupper($color);
    }
}

<?php

class CReport_Generator_Processor_PdfProcessor extends CReport_Generator_ProcessorAbstract {
    protected $tcpdf;

    protected $currentY;

    protected $pageHeight;

    protected $pageWidth;

    protected $topMargin;

    protected $leftMargin;

    protected $rightMargin;

    protected $bottomMargin;

    protected $currentX;

    public function __construct(CReport_Builder_Report $report) {
        parent::__construct($report);
        $this->tcpdf = new CReport_Pdf_Adapter_TCPDF(
            $report->getOrientation() == CReport::ORIENTATION_LANDSCAPE ? 'L' : 'P',
            'pt',
            [$report->getPageWidth(), $report->getPageHeight()],
            $unicode = true,
            $encoding = 'UTF-8',
            $diskcache = false,
            $pdfa = false
        );
        $this->pageHeight = $report->getOrientation() == CReport::ORIENTATION_LANDSCAPE ? $report->getPageWidth() : $report->getPageHeight();
        $this->pageWidth = $report->getOrientation() == CReport::ORIENTATION_LANDSCAPE ? $report->getPageHeight() : $report->getPageWidth();
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetLeftMargin($report->getLeftMargin());
        $this->tcpdf->SetRightMargin($report->getRightMargin());
        $this->tcpdf->SetTopMargin($report->getTopMargin());
        $this->tcpdf->SetAutoPageBreak(true, $report->getBottomMargin() > 0 ? $report->getBottomMargin() / 2 : 0);
        $this->tcpdf->AddPage();
        $this->tcpdf->setPage(1, true);
        $this->topMargin = $report->getTopMargin();
        $this->bottomMargin = $report->getBottomMargin();
        $this->leftMargin = $report->getLeftMargin();
        $this->rightMargin = $report->getRightMargin();
        $this->currentY = $this->topMargin;
        $this->currentX = $this->leftMargin;
    }

    private function getPdfTextAlignment($alignment) {
        $alignmentMap = [
            CREPORT::TEXT_ALIGNMENT_CENTER => 'C',
            CREPORT::TEXT_ALIGNMENT_LEFT => 'L',
            CREPORT::TEXT_ALIGNMENT_RIGHT => 'R',
            CREPORT::TEXT_ALIGNMENT_JUSTIFY => 'J',
        ];

        return carr::get($alignmentMap, $alignment, 'L');
    }

    private function getPdfVerticalAlignment($alignment) {
        $alignmentMap = [
            CREPORT::VERTICAL_ALIGNMENT_BOTTOM => 'B',
            CREPORT::VERTICAL_ALIGNMENT_MIDDLE => 'M',
            CREPORT::VERTICAL_ALIGNMENT_TOP => 'T',
        ];

        return carr::get($alignmentMap, $alignment, 'T');
    }

    /**
     * Return format for a component of a box.
     *
     * @param CReport_Builder_Object_Pen      $pen
     * @param null|CReport_Builder_Object_Box $box
     *
     * @return int[]|string[]|int[][]
     */
    public static function getPdfPen(CReport_Builder_Object_Pen $pen, CReport_Builder_Object_Box $box = null) {
        $lineColor = $pen->getLineColor();
        if ($lineColor) {
            $lineColorRgb = CColor::create($lineColor)->toRgb();
            $drawcolor = [
                'r' => $lineColorRgb->red(),
                'g' => $lineColorRgb->green(),
                'b' => $lineColorRgb->blue()
            ];
        }

        $dash = '';
        $lineStyle = $pen->getLineStyle();
        if ($lineStyle) {
            // Dotted Dashed
            if ($lineStyle == CReport::LINE_STYLE_DOTTED) {
                $dash = '1,1';
            } elseif ($lineStyle == CReport::LINE_STYLE_DASHED) {
                $dash = '4,2';
            }
        }

        return [
            'width' => $pen->getLineWidth(),
            'cap' => 'butt',
            'join' => 'miter',
            'dash' => $dash,
            'phase' => 0,
            'color' => $drawcolor
        ];
    }

    /**
     * Returns patterns for all borders of a box.
     *
     * @param CReport_Builder_Object_Box $box
     *
     * @return array[]
     */
    private function getPdfBorder(CReport_Builder_Object_Box $box) {
        $border = [];
        $pen = $box->getPen();
        $topPen = $box->getTopPen() ?: $pen;
        $leftPen = $box->getLeftPen() ?: $pen;
        $bottomPen = $box->getBottomPen() ?: $pen;
        $rightPen = $box->getRightPen() ?: $pen;
        if ($topPen && $topPen->getLineWidth() > 0.0) {
            $border['T'] = $this->getPdfPen($topPen);
        }
        if ($leftPen && $leftPen->getLineWidth() > 0.0) {
            $border['L'] = $this->getPdfPen($leftPen);
        }
        if ($bottomPen && $bottomPen->getLineWidth() > 0.0) {
            $border['B'] = $this->getPdfPen($bottomPen);
        }
        if ($rightPen && $rightPen->getLineWidth() > 0.0) {
            $border['R'] = $this->getPdfPen($rightPen);
        }

        return $border;
    }

    public function preventYOverflow(CReport_Generator $generator, $height) {
        $preventYAxis = $this->currentY + $height;
        $pageHeight = $this->pageHeight;
        //$pageFooter = $this->jasperReport->getRoot()->getChildByClassName('PageFooter');
        $pageFooter = null;
        $pageFooterHeight = 0;
        $topMargin = $this->topMargin;
        $bottomMargin = $this->bottomMargin;
        $discount = $pageHeight - $pageFooterHeight - $topMargin - $bottomMargin; //dicount heights of page parts;
        // var_dump($pageFooter);
        //exit;

        if ($preventYAxis >= $discount) {
            // cdbg::dd($preventYAxis, $discount, $pageheight, $pageFooterHeigth, $topMargin, $bottomMargin);

            if ($pageFooter) {
                // CReport_Jasper_Instructions::$lastPageFooter = false;
                // $pageFooter->generate($this->jasperReport);
            }
            $this->resetY();
            $generator->incrementPageNumber();
            $this->tcpdf->AddPage();
            $this->tcpdf->setPage($generator->getPageNumber(), $resetMargin = false);

            // $pageHeader = $this->jasperReport->getRoot()->getChildByClassName('PageHeader');
            // if ($pageHeader) {
            //     $pageHeader->generate($this->jasperReport);
            // }
            // //repeat column header?
            // if ($this->jasperReport::$columnHeaderRepeat) {
            //     $columnHeader = $this->jasperReport->getRoot()->getChildByClassName('ColumnHeader');
            //     if ($columnHeader) {
            //         $columnHeader->generate($this->jasperReport);
            //     }
            // }
            // CReport_Jasper_Instructions::runInstructions();
        }
    }

    public function resetY() {
        $this->currentY = $this->topMargin;
    }

    /**
     * @param float $height
     *
     * @return float
     */
    public function addY($height) {
        if ($this->currentY + $height <= $this->pageHeight) {
            $this->currentY += $height;
        }

        return $this->currentY;
    }

    public function font(CReport_Builder_Object_Font $font) {
        $fontName = $font->getFontName();
        $fontSize = $font->getFontSize();
        // $fontfile = CReport_Jasper_Instructions::$fontdir . '/' . $arraydata['font'] . '.php';
        // if(file_exists($fontfile) || $this->jasperReport->bypassnofont==false){

        // $fontfile = CReport_Jasper_Instructions::$fontdir . '/' . $arraydata['font'] . '.php';

        // $this->tcpdf->SetFont($arraydata['font'], $arraydata['fontstyle'], $arraydata['fontsize'], $fontfile);
        $fontStyle = '';
        if ($font->isBold()) {
            $fontStyle .= 'B';
        }
        if ($font->isItalic()) {
            $fontStyle .= 'I';
        }
        if ($font->isUnderline()) {
            $fontStyle .= 'U';
        }
        if ($font->isStrikeThrough()) {
            $fontStyle .= 'S';
        }
        //TODO FOR Strike Through
        $this->tcpdf->setFont(
            $fontName,
            $fontStyle,
            $fontSize,
            $fontfile = '',
            $subset = 'default',
            $out = true
        );
    }

    public function cellHeight(array $options) {
        $text = carr::get($options, 'text');
        $width = carr::get($options, 'width');
        $font = carr::get($options, 'font');
        $box = carr::get($options, 'box');
        $lineSpacing = carr::get($options, 'lineSpacing');

        $pdfBorder = 0;
        $pdfPadding = null;
        if ($box && $box instanceof CReport_Builder_Object_Box) {
            $padding = $box->getPadding();
            $pdfPadding = [
                'T' => $padding->getPaddingTop(),
                'R' => $padding->getPaddingRight(),
                'L' => $padding->getPaddingLeft(),
                'B' => $padding->getPaddingBottom(),
            ];
            $pdfBorder = $this->getPdfBorder($box);
            // $this->tcpdf->setCellPaddings($padding->getPaddingLeft(), $padding->getPaddingTop(), $padding->getPaddingRight(), $padding->getPaddingBottom());
        }

        $reseth = true;
        $autopadding = true;

        if ($font != null) {
            $this->font($font);
        }
        $this->tcpdf->setCellHeightRatio($lineSpacing);
        $cellHeight = $this->tcpdf->getStringHeight(
            $width,
            $text,
            $reseth,
            $autopadding,
            $pdfPadding,
            $pdfBorder
        );

        return $cellHeight;
    }

    public function cell(array $options) {
        $text = carr::get($options, 'text');
        $width = carr::get($options, 'width');
        $height = carr::get($options, 'height');
        $font = carr::get($options, 'font');
        $backgroundColor = carr::get($options, 'backgroundColor');
        $x = carr::get($options, 'x');
        $y = carr::get($options, 'y');
        $pdfX = $this->leftMargin + $x;
        $pdfY = $this->currentY + $y;
        $box = carr::get($options, 'box');
        $lineSpacing = carr::get($options, 'lineSpacing');

        $pdfTextAlignment = $this->getPdfTextAlignment(carr::get($options, 'textAlignment'));
        $pdfVerticalAlignment = $this->getPdfVerticalAlignment(carr::get($options, 'verticalAlignment'));
        $fill = 0;
        // if ($x != null) {
        //     $this->tcpdf->setX($x);
        // }
        // if ($y != null) {
        //     $this->tcpdf->setY($y);
        // }
        // CReport_Jasper_Instructions::$objOutPut->SetXY($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis);
        $pdfBorder = 0;
        if ($box && $box instanceof CReport_Builder_Object_Box) {
            $padding = $box->getPadding();

            $pdfBorder = $this->getPdfBorder($box);
            $this->tcpdf->setCellPaddings($padding->getPaddingLeft(), $padding->getPaddingTop(), $padding->getPaddingRight(), $padding->getPaddingBottom());
        }
        if ($backgroundColor) {
            $fill = 1;
            $color = CColor::create($backgroundColor);
            $color = $color->toRgb();
            $this->tcpdf->SetFillColor($color->red(), $color->green(), $color->blue());
        }
        $this->tcpdf->setCellHeightRatio($lineSpacing);

        if ($font != null) {
            $this->font($font);
        }

        $maxHeight = $height;
        $ln = 0;

        $this->tcpdf->MultiCell(
            $width,
            $height,
            $text,
            $pdfBorder,
            $pdfTextAlignment,
            $fill,
            $ln,
            $pdfX,
            $pdfY,
            $reseth = true,
            $stretch = 0,
            $ishtml = false,
            $autopadding = true,
            $maxHeight,
            $pdfVerticalAlignment,
            $fitcell = false
        );
    }

    public function image(array $options) {
        $src = carr::get($options, 'src');
        $x = carr::get($options, 'x');
        $y = carr::get($options, 'y');
        $width = carr::get($options, 'width');
        $height = carr::get($options, 'height');
        $verticalAlignment = carr::get($options, 'verticalAlignment');
        $horizontalAlignment = carr::get($options, 'horizontalAlignment');
        $scaleImage = carr::get($options, 'scaleImage');
        $fitbox = false;
        if ($scaleImage != CReport::SCALE_IMAGE_FILL_FRAME) {
            //check the alignment
            $hAlignMap = [
                CREPORT::HORIZONTAL_ALIGNMENT_LEFT => 'L',
                CREPORT::HORIZONTAL_ALIGNMENT_RIGHT => 'R',
                CREPORT::HORIZONTAL_ALIGNMENT_CENTER => 'C',
            ];
            $vAlignMap = [
                CREPORT::VERTICAL_ALIGNMENT_TOP => 'T',
                CREPORT::VERTICAL_ALIGNMENT_MIDDLE => 'M',
                CREPORT::VERTICAL_ALIGNMENT_BOTTOM => 'B',
            ];
            $fitbox = carr::get($hAlignMap, $horizontalAlignment, 'L') . carr::get($vAlignMap, $verticalAlignment, 'T');
        }
        $x = $this->leftMargin + $x;
        $y = $this->currentY + $y;
        if (CFile::exists($src)) {
            $this->tcpdf->Image(
                $src,
                $x,
                $y,
                $width,
                $height,
                $type = '',
                $link = '',
                $align = '',
                $resize = false,
                $dpi = 300,
                $palign = '',
                $ismask = false,
                $imgmask = false,
                $border = 0,
                $fitbox,
                $hidden = false,
                $fitonpage = false,
                $alt = false,
                $altimgs = []
            );
        }
    }

    /**
     * @return CReport_Pdf_Adapter_TCPDF
     */
    public function getOutput() {
        return $this->tcpdf;
    }
}

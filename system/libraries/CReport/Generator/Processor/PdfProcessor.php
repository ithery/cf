<?php

class CReport_Generator_Processor_PdfProcessor extends CReport_Generator_ProcessorAbstract {
    protected $tcpdf;

    protected $currentY;

    protected $pageHeight;

    protected $pageWidth;

    protected $currentX;

    protected $offsetX;

    protected $offsetY;

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
        $this->offsetY = $report->getTopMargin();
        $this->offsetX = $report->getLeftMargin();
        $this->currentY = $this->offsetY;
        $this->currentX = $this->offsetX;
    }

    protected function prepare() {
        CReport_Jasper_Instructions::$arrayPageSetting = $this->jasperReport->arrayPageSetting;
        if ($this->jasperReport->arrayPageSetting['orientation'] == 'Landscape') {
            CReport_Jasper_Instructions::$objOutPut = new CReport_Pdf_Adapter_TCPDF(
                $this->jasperReport->arrayPageSetting['orientation'],
                'pt',
                [intval($this->jasperReport->arrayPageSetting['pageHeight']), intval($this->jasperReport->arrayPageSetting['pageWidth'])],
                true
            );
        } else {
            CReport_Jasper_Instructions::$objOutPut = new CReport_Pdf_Adapter_TCPDF($this->jasperReport->arrayPageSetting['orientation'], 'pt', [intval($this->jasperReport->arrayPageSetting['pageWidth']), intval($this->jasperReport->arrayPageSetting['pageHeight'])], true);
        }
        CReport_Jasper_Instructions::$objOutPut->SetLeftMargin((int) $this->jasperReport->arrayPageSetting['leftMargin']);
        CReport_Jasper_Instructions::$objOutPut->SetRightMargin((int) $this->jasperReport->arrayPageSetting['rightMargin']);
        CReport_Jasper_Instructions::$objOutPut->SetTopMargin((int) $this->jasperReport->arrayPageSetting['topMargin']);
        CReport_Jasper_Instructions::$objOutPut->SetAutoPageBreak(true, (int) $this->jasperReport->arrayPageSetting['bottomMargin'] / 2);
        //self::$pdfOutPut->AliasNumPage();
        CReport_Jasper_Instructions::$objOutPut->setPrintHeader(false);
        CReport_Jasper_Instructions::$objOutPut->setPrintFooter(false);
        CReport_Jasper_Instructions::$objOutPut->AddPage();
        CReport_Jasper_Instructions::$objOutPut->setPage(1, true);
        CReport_Jasper_Instructions::$yAxis = (int) $this->jasperReport->arrayPageSetting['topMargin'];

        if (CReport_Jasper_Instructions::$fontdir == '') {
            CReport_Jasper_Instructions::$fontdir = dirname(__FILE__) . '/tcpdf/fonts';
        }
    }

    private function getAlign($alignment) {
        $alignmentMap = [
            CREPORT::TEXT_ALIGNMENT_CENTER => 'C',
            CREPORT::TEXT_ALIGNMENT_LEFT => 'L',
            CREPORT::TEXT_ALIGNMENT_RIGHT => 'R',
            CREPORT::TEXT_ALIGNMENT_JUSTIFY => 'J',
        ];

        return carr::get($alignmentMap, $alignment, 'L');
    }

    /**
     * @param float $height
     *
     * @return flaot
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

    public function cell(array $options) {
        $text = carr::get($options, 'text');
        $width = carr::get($options, 'width');
        $height = carr::get($options, 'height');
        $font = carr::get($options, 'font');
        $backgroundColor = carr::get($options, 'backgroundColor');
        $x = carr::get($options, 'x');
        $y = carr::get($options, 'y');
        $x = $this->offsetX + $x;
        $y = $this->currentY + $y;
        $box = carr::get($options, 'box');
        $lineSpacing = carr::get($options, 'lineSpacing');

        $textAlignment = $this->getAlign(carr::get($options, 'textAlignment'));
        $fill = 0;
        // if ($x != null) {
        //     $this->tcpdf->setX($x);
        // }
        // if ($y != null) {
        //     $this->tcpdf->setY($y);
        // }
        // CReport_Jasper_Instructions::$objOutPut->SetXY($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis);
        if ($box && $box instanceof CReport_Builder_Object_Box) {
            $padding = $box->getPadding();
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
        $this->tcpdf->MultiCell(
            $width,
            $height,
            $text,
            $border = 0,
            $textAlignment,
            $fill,
            $ln = 1,
            $x,
            $y,
            $reseth = true,
            $stretch = 0,
            $ishtml = false,
            $autopadding = true,
            $maxh = 0,
            $valign = 'T',
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
        $x = $this->offsetX + $x;
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

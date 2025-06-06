<?php

class CReport_Generator_Processor_PdfProcessor extends CReport_Generator_ProcessorAbstract {
    /**
     * @var CReport_Adapter_Pdf_TCPDF
     */
    protected $tcpdf;

    public function __construct(CReport_Builder_Report $report) {
        parent::__construct($report);
        $this->tcpdf = new CReport_Adapter_Pdf_TCPDF(
            $report->getOrientation() == CReport::ORIENTATION_LANDSCAPE ? 'L' : 'P',
            'pt',
            [$report->getPageWidth(), $report->getPageHeight()],
            $unicode = true,
            $encoding = 'UTF-8',
            $diskcache = false,
            $pdfa = false
        );
        $this->tcpdf->setPrintHeader(false);
        $this->tcpdf->setPrintFooter(false);
        $this->tcpdf->SetLeftMargin($report->getLeftMargin());
        $this->tcpdf->SetRightMargin($report->getRightMargin());
        $this->tcpdf->SetTopMargin($report->getTopMargin());
        $this->tcpdf->SetAutoPageBreak(true, $report->getBottomMargin() > 0 ? $report->getBottomMargin() / 2 : 0);
        $this->tcpdf->AddPage();
        $this->tcpdf->setPage(1, true);
    }

    public function setPage($page) {
        $this->tcpdf->setPage($page);
    }

    private function getPdfTextAlignment($alignment) {
        $alignmentMap = [
            CREPORT::TEXT_ALIGNMENT_CENTER => 'C',
            CREPORT::TEXT_ALIGNMENT_LEFT => 'L',
            CREPORT::TEXT_ALIGNMENT_RIGHT => 'R',
            CREPORT::TEXT_ALIGNMENT_JUSTIFIED => 'J',
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
     * @param CReport_Builder_Object_Pen $pen
     *
     * @return int[]|string[]|int[][]
     */
    public static function getPdfPen(CReport_Builder_Object_Pen $pen) {
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
            //butt,round,square
            'cap' => 'butt',
            //miter,round,bevel
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

    public function willChangePage(CReport_Generator $generator, $height) {
        $preventYAxis = $this->currentY + $height;
        $pageHeight = $this->pageHeight;
        $pageFooter = $generator->getPageFooter();
        $columnFooter = $generator->isColumnFooterDrawn() ? null : $generator->getColumnFooter();
        $pageFooterHeight = 0;
        $columnFooterHeight = 0;
        if ($pageFooter) {
            $pageFooterHeight = $pageFooter->getHeight();
        }
        if ($columnFooter) {
            $columnFooterHeight = $columnFooter->getHeight();
        }
        $topMargin = $this->topMargin;
        $bottomMargin = $this->bottomMargin;
        $discount = $pageHeight - $pageFooterHeight - $columnFooterHeight - $topMargin - $bottomMargin; //dicount heights of page parts;
        // var_dump($pageFooter);
        //exit;

        return $preventYAxis >= $discount;
    }

    public function preventYOverflow(CReport_Generator $generator, $height) {
        // $preventYAxis = $this->currentY + $height;
        // $pageHeight = $this->pageHeight;
        $pageFooter = $generator->getPageFooter();
        $columnFooter = $generator->isColumnFooterDrawn() ? null : $generator->getColumnFooter();
        // $pageFooterHeight = 0;
        // $columnFooterHeight = 0;
        // if ($pageFooter) {
        //     $pageFooterHeight = $pageFooter->getHeight();
        // }
        // if ($columnFooter) {
        //     $columnFooterHeight = $columnFooter->getHeight();
        // }
        // $topMargin = $this->topMargin;
        // $bottomMargin = $this->bottomMargin;
        // $discount = $pageHeight - $pageFooterHeight - $columnFooterHeight - $topMargin - $bottomMargin; //dicount heights of page parts;
        // var_dump($pageFooter);
        //exit;

        if ($this->willChangePage($generator, $height)) {
            if ($columnFooter) {
                $columnFooter->generate($generator, $this);
            }

            if ($pageFooter) {
                $pageFooter->generate($generator, $this);
            }
            $this->resetY();
            $generator->incrementPageNumber();
            $this->tcpdf->AddPage();
            $this->tcpdf->setPage($generator->getPageNumber(), $resetMargin = false);

            $pageHeader = $generator->getPageHeader();
            if ($pageHeader) {
                $pageHeader->generate($generator, $this);
            }
            if ($generator->isProcessingDetail()) {
                $columnHeader = $generator->getColumnHeader();
                if ($columnHeader) {
                    $columnHeader->generate($generator, $this);
                }
                $groups = $generator->getGroups();
                foreach ($groups as $group) {
                    if ($group->isReprintHeaderOnEachPage() && $group->hasGroupHeader()) {
                        $groupHeader = $group->getGroupHeaderElement();
                        $groupHeader->generate($generator, $this);
                    }
                }
            }

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

    /**
     * @param float $y
     *
     * @return float
     */
    public function setY($y) {
        return $this->currentY = $y;
    }

    public function font(CReport_Builder_Object_Font $font) {
        $fontName = $font->getFontName();
        $fontSize = $font->getFontSize();
        $fontFile = $font->getFontFile();
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
        // if ($fontName == 'pckktqlucidasanstypewriterb') {
        //     cdbg::dd(CFile::exists('/home/dev/public_html/application/aidnity/default/data/fonts/pckktqlucidasanstypewriterb/pckktqlucidasanstypewriterb.php'));
        //     cdbg::dd(CReport_Pdf_FontManager::instance()->all());
        //     cdbg::dd($fontFile);
        // }
        //TODO FOR Strike Through
        $this->tcpdf->setFont(
            $fontName,
            $fontStyle,
            $fontSize,
            $fontFile,
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
        $letterSpacing = carr::get($options, 'letterSpacing');
        $wordSpacing = carr::get($options, 'wordSpacing');
        $backgroundColor = carr::get($options, 'backgroundColor');
        $foregroundColor = carr::get($options, 'foregroundColor');
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

        if ($foregroundColor) {
            $color = CColor::create($foregroundColor);
            $color = $color->toRgb();
            $this->tcpdf->setTextColor($color->red(), $color->green(), $color->blue());
        }

        if ($letterSpacing !== null) {
            $this->tcpdf->setFontSpacing($letterSpacing);
        }
        if ($wordSpacing !== null) {
            $this->tcpdf->setWordSpacing($wordSpacing);
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
        $this->resetTextColor();
    }

    public function line(array $options) {
        $x = carr::get($options, 'x');
        $y = carr::get($options, 'y');
        $x1 = $this->leftMargin + $x;
        $y1 = $this->currentY + $y;

        $width = carr::get($options, 'width');
        $height = carr::get($options, 'height');
        $x2 = $x1 + $width;
        $y2 = $y1 + $height;
        $pen = carr::get($options, 'pen');
        $style = [];
        if ($pen != null && $pen instanceof CReport_Builder_Object_Pen) {
            $style = $this->getPdfPen($pen);
        }

        $this->tcpdf->Line(
            $x1,
            $y1,
            $x2,
            $y2,
            $style
        );
    }

    public function rectangle(array $options) {
        $x = carr::get($options, 'x');
        $y = carr::get($options, 'y');

        $pdfX = $this->leftMargin + $x;
        $pdfY = $this->currentY + $y;
        $radius = carr::get($options, 'radius');

        $width = carr::get($options, 'width');
        $height = carr::get($options, 'height');
        $pen = carr::get($options, 'pen');
        $style = [];
        $borderStyle = [];
        if ($pen != null && $pen instanceof CReport_Builder_Object_Pen) {
            $borderStyle = $this->getPdfPen($pen);
        }
        $style = '';
        $roundCorner = '1111';

        $fillColor = [];

        $this->tcpdf->RoundedRect(
            $pdfX,
            $pdfY,
            $width,
            $height,
            $radius,
            $roundCorner,
            $style,
            $borderStyle,
            $fillColor,
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
     * @return CReport_Adapter_Pdf_TCPDF
     */
    public function getOutput() {
        return $this->tcpdf;
    }

    public function resetTextColor() {
        $this->tcpdf->setTextColor(0, 0, 0);
    }

    public function getY() {
        return $this->currentY;
    }

    /**
     * @param string $fontName
     * @param string $fontPath
     *
     * @return void
     */
    public function addFont($fontName, $fontPath) {
        $style = '';
        $subset = 'default';
        $this->tcpdf->AddFont($fontName, $style, $fontPath, $subset);
    }

    public function raw($content) {
        $this->tcpdf->raw($content);
    }
}

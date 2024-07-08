<?php

class CReport_Jasper_Processor_PdfProcessor extends CReport_Jasper_ProcessorAbstract {
    private $print_expression_result;

    protected function prepare() {
        CReport_Jasper_Instructions::$arrayPageSetting = $this->jasperReport->arrayPageSetting;
        if ($this->jasperReport->arrayPageSetting['orientation'] == 'Landscape') {
            CReport_Jasper_Instructions::$objOutPut = new CReport_Pdf_Adapter_TCPDF($this->jasperReport->arrayPageSetting['orientation'], 'pt', [intval($this->jasperReport->arrayPageSetting['pageHeight']), intval($this->jasperReport->arrayPageSetting['pageWidth'])], true);
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

    public static function pageNo() {
        CReport_Jasper_Instructions::$objOutPut->PageNo();
    }

    public static function get() {
        return CReport_Jasper_Instructions::$objOutPut;
    }

    public function preventYAxis($arraydata) {
        //$pdf = \JasperPHP\Pdf;
        $preventYAxis = CReport_Jasper_Instructions ::$yAxis + $arraydata['y_axis'];
        $pageheight = (float) CReport_Jasper_Instructions::$arrayPageSetting['pageHeight'];
        $pageFooter = $this->jasperReport->getRoot()->getChildByClassName('PageFooter');
        $pageFooterHeigth = ($pageFooter) ? $pageFooter->children[0]->height : 0;
        $topMargin = (float) CReport_Jasper_Instructions::$arrayPageSetting['topMargin'];
        $bottomMargin = (float) CReport_Jasper_Instructions::$arrayPageSetting['bottomMargin'];
        $discount = $pageheight - $pageFooterHeigth - $topMargin - $bottomMargin; //dicount heights of page parts;
        // var_dump($pageFooter);
        //exit;

        if ($preventYAxis >= $discount) {
            // cdbg::dd($preventYAxis, $discount, $pageheight, $pageFooterHeigth, $topMargin, $bottomMargin);

            if ($pageFooter) {
                CReport_Jasper_Instructions::$lastPageFooter = false;
                $pageFooter->generate($this->jasperReport);
            }
            CReport_Jasper_Instructions::addInstruction(['type' => 'resetYAxis']);
            CReport_Jasper_Instructions::$currrentPage++;
            CReport_Jasper_Instructions::addInstruction(['type' => 'addPage']);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setPage', 'value' => CReport_Jasper_Instructions::$currrentPage, 'resetMargins' => false]);
            CReport_Jasper_Instructions::runInstructions();

            $pageHeader = $this->jasperReport->getRoot()->getChildByClassName('PageHeader');
            if ($pageHeader) {
                $pageHeader->generate($this->jasperReport);
            }
            //repeat column header?
            if ($this->jasperReport::$columnHeaderRepeat) {
                $columnHeader = $this->jasperReport->getRoot()->getChildByClassName('ColumnHeader');
                if ($columnHeader) {
                    $columnHeader->generate($this->jasperReport);
                }
            }
            CReport_Jasper_Instructions::runInstructions();
        }
    }

    public function resetYAxis($arraydata) {
        CReport_Jasper_Instructions::$yAxis = (int) CReport_Jasper_Instructions::$arrayPageSetting['topMargin'];
    }

    public function setYAxis($arraydata) {
        if ((CReport_Jasper_Instructions::$yAxis + $arraydata['y_axis']) <= CReport_Jasper_Instructions::$arrayPageSetting['pageHeight']) {
            CReport_Jasper_Instructions::$yAxis = CReport_Jasper_Instructions::$yAxis + $arraydata['y_axis'];
        }
    }

    public function changeColumn($arraydata) {
        if (CReport_Jasper_Instructions::$arrayPageSetting['columnCount'] > (CReport_Jasper_Instructions::$arrayPageSetting['CollumnNumber'])) {
            CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'] = CReport_Jasper_Instructions::$arrayPageSetting['defaultLeftMargin'] + (CReport_Jasper_Instructions::$arrayPageSetting['columnWidth'] * CReport_Jasper_Instructions::$arrayPageSetting['CollumnNumber']);
            CReport_Jasper_Instructions::$arrayPageSetting['CollumnNumber'] = CReport_Jasper_Instructions::$arrayPageSetting['CollumnNumber'] + 1;
        } else {
            CReport_Jasper_Instructions::$arrayPageSetting['CollumnNumber'] = 1;
            CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'] = CReport_Jasper_Instructions::$arrayPageSetting['defaultLeftMargin'];
        }
    }

    public function addPage($arraydata) {
        $this->jasperReport->pageChanged = true;

        // $pdf = JasperPHP\Pdf;
        CReport_Jasper_Instructions::$objOutPut->AddPage();
    }

    public function setPage($arraydata) {
        //$pdf = JasperPHP\Pdf;
        CReport_Jasper_Instructions::$objOutPut->setPage($arraydata['value'], $arraydata['resetMargins']);
    }

    public function setFont($arraydata) {
        $arraydata['font'] = strtolower($arraydata['font']);

        $fontfile = CReport_Jasper_Instructions::$fontdir . '/' . $arraydata['font'] . '.php';
        // if(file_exists($fontfile) || $this->jasperReport->bypassnofont==false){

        $fontfile = CReport_Jasper_Instructions::$fontdir . '/' . $arraydata['font'] . '.php';

        CReport_Jasper_Instructions::$objOutPut->SetFont($arraydata['font'], $arraydata['fontstyle'], $arraydata['fontsize'], $fontfile);
        /* }
          else{
          $arraydata["font"]="freeserif";
          if($arraydata["fontstyle"]=="")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserif',$arraydata["fontstyle"],$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserif.php');
          elseif($arraydata["fontstyle"]=="B")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserifb',$arraydata["fontstyle"],$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserifb.php');
          elseif($arraydata["fontstyle"]=="I")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserifi',$arraydata["fontstyle"],$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserifi.php');
          elseif($arraydata["fontstyle"]=="BI")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserifbi',$arraydata["fontstyle"],$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserifbi.php');
          elseif($arraydata["fontstyle"]=="BIU")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserifbi',"BIU",$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserifbi.php');
          elseif($arraydata["fontstyle"]=="U")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserif',"U",$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserif.php');
          elseif($arraydata["fontstyle"]=="BU")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserifb',"U",$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserifb.php');
          elseif($arraydata["fontstyle"]=="IU")
          JasperPHP\Pdf::$pdfOutPut->SetFont('freeserifi',"IU",$arraydata["fontsize"],JasperPHP\Pdf::$fontdir.'/freeserifbi.php');


          } */
    }

    public function setCellHeightRatio($arraydata) {
        CReport_Jasper_Instructions::$objOutPut->SetCellHeightRatio($arraydata['ratio']);
    }

    public function getHeightMultiCell($obj) {
        /** @var \TCPDF $pdf */
        $pdf = clone CReport_Jasper_Instructions::$objOutPut;
        $JasperObj = $this->jasperReport;
        // var_dump($obj->children);
        $txt = (string) $obj['txt'];
        $lineSpacing = $obj['lineSpacing'];
        $debug = carr::get($obj, 'debug', false);
        $fontSize = carr::get($obj, 'fontSize', null);
        //$newfont = $JasperObj->recommendFont($txt, null, null);
        //$pdf->SetFont($newfont,$pdf->getFontStyle(),$this->defaultFontSize);
        $this->printExpression($obj);
        $arraydata = $obj;

        //$pdf->SetXY($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        //default
        $pLeft = 1;
        $pTop = 0;
        $pRight = 1;
        $pBottom = 0;
        $multiCellHeight = 0;
        //suport padding cells
        if (isset($obj['box']) && !empty($obj['box'])) {
            if (isset($obj['box']['leftPadding'])) {
                $pLeft = $obj['box']['leftPadding'];
            }
            if (isset($obj['box']['topPadding'])) {
                $pTop = $obj['box']['topPadding'];
            }
            if (isset($obj['box']['rightPadding'])) {
                $pRight = $obj['box']['rightPadding'];
            }
            if (isset($obj['box']['bottomPadding'])) {
                $pBottom = $obj['box']['bottomPadding'];
            }
        }
        $pdf->setCellPaddings($pLeft, $pTop, $pRight, $pBottom);
        $w = $arraydata['width'];
        $h = $arraydata['height'];
        $pdf->StartTransform();

        $clipx = $arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'];
        $clipy = $arraydata['y'] + CReport_Jasper_Instructions::$yAxis;
        $clipw = $arraydata['width'];
        $cliph = $arraydata['height'];

        $rotated = false;
        if ($this->print_expression_result == true) {
            $angle = $this->rotate($arraydata);
            if ($angle != 0) {
                $pdf->Rect($clipx, $clipy, $clipw, $cliph, 'CNZ');
                $pdf->Rotate($angle);
                $rotated = true;
                switch ($angle) {
                    case 90:
                        $x = $x - $arraydata['height'];
                        $h = $arraydata['width'];
                        $w = $arraydata['height'];

                        break;
                    case 180:
                        $x = $x - $arraydata['width'];
                        $y = $y - $arraydata['height'];

                        break;
                    case 270:
                        $y = $y - $arraydata['width'];
                        $h = $arraydata['width'];
                        $w = $arraydata['height'];

                        break;
                }
            }
            // echo $arraydata["link"];
            if ($arraydata['link']) {
                //print_r($arraydata);
                //$this->debughyperlink=true;
                //  echo $arraydata["link"].",print:".$this->print_expression_result;
                //$arraydata["link"] = $JasperObj->analyse_expression($arraydata["link"], "");
                //$this->debughyperlink=false;
            }
            //print_r($arraydata);

            if ($arraydata['writeHTML'] == true) {
                //echo  ($txt);
                $pdf->writeHTML($txt, true, 0, true, true);
                $pdf->Ln();
            } elseif ($arraydata['poverflow'] == 'false' && $arraydata['soverflow'] == 'false') {
                if ($arraydata['valign'] == 'C') {
                    $arraydata['valign'] = 'M';
                }
                if ($arraydata['valign'] == '') {
                    $arraydata['valign'] = 'T';
                }

                // clip width & height
                if (!$rotated) {
                    $pdf->Rect($clipx, $clipy, $clipw, $cliph, 'CNZ');
                }

                $pattern = (array_key_exists('pattern', $arraydata)) ? $arraydata['pattern'] : '';
                $text = $pattern != '' ? CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $pattern) : $txt;

                $tempCellHeightRatio = null;
                if ($lineSpacing) {
                    $tempCellHeightRatio = $pdf->getCellHeightRatio();
                    $pdf->setCellHeightRatio($lineSpacing);
                }

                $autopadding = false;
                $cellpadding = ['T' => 0, 'R' => 0, 'B' => 0, 'L' => 0];
                $border = 0;
                $reseth = false;
                $tempFontSize = null;
                if ($fontSize) {
                    $tempFontSize = $pdf->getFontSize();
                    $pdf->setFontSize($fontSize);
                }
                $multiCellHeight = $pdf->getStringHeight(
                    $w,
                    $text,
                    $reseth,
                    $autopadding,
                    $cellpadding,
                    $border
                );

                if ($tempFontSize) {
                    $pdf->setFontSize($tempFontSize);
                }
                if ($tempCellHeightRatio) {
                    $pdf->setCellHeightRatio($tempCellHeightRatio);
                }
            } elseif ($arraydata['poverflow'] == 'true' || $arraydata['soverflow'] == 'true') {
                if ($arraydata['valign'] == 'C') {
                    $arraydata['valign'] = 'M';
                }
                if ($arraydata['valign'] == '') {
                    $arraydata['valign'] = 'T';
                }

                $x = $pdf->GetX();
                $yAfter = $pdf->GetY();
                $maxheight = array_key_exists('maxheight', $arraydata) ? $arraydata['maxheight'] : 0;
                //if($arraydata["link"])   echo $arraydata["linktarget"].",".$arraydata["link"]."<br/><br/>";
                //$pdf->MultiCell($w, $h, CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $arraydata['pattern']), $arraydata['border'], $arraydata['align'], $arraydata['fill'], 1, $x, $y, true, 0, false, true, $maxheight); //,$arraydata["valign"]);
                //getStringHeight(float $w, string $txt[, bool $reseth = false ][, bool $autopadding = true ][, array<string|int, mixed>|null $cellpadding = null ][, mixed $border = 0 ])
                $tempCellHeightRatio = null;
                if ($lineSpacing) {
                    $tempCellHeightRatio = $pdf->getCellHeightRatio();
                    $pdf->setCellHeightRatio($lineSpacing);
                }

                $autopadding = false;
                $cellpadding = ['T' => 0, 'R' => 0, 'B' => 0, 'L' => 0];
                $border = 0;
                $reseth = false;
                $autopadding = false;
                $tempFontSize = null;
                if ($fontSize) {
                    $tempFontSize = $pdf->getFontSize();
                    $pdf->setFontSize($fontSize);
                }
                $pattern = (array_key_exists('pattern', $arraydata)) ? $arraydata['pattern'] : '';
                $text = $pattern != '' ? CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $pattern) : $txt;

                $multiCellHeight = $pdf->getStringHeight(
                    $w,
                    $text,
                    $reseth,
                    $autopadding,
                    $cellpadding,
                    $border
                );
                // if ($debug) {
                //     cdbg::dd(
                //         $lineSpacing,
                //         $multiCellHeight,
                //         $txt,
                //         $pdf->getNumLines($txt, $w, $reseth, $autopadding, $cellpadding, $border),
                //         $pdf->getCellPaddings(),
                //         $pdf->getFontSize(),
                //         $pdf->getCellHeightRatio(),
                //     );
                // }
                if ($tempFontSize) {
                    $pdf->setFontSize($tempFontSize);
                }
                if ($tempCellHeightRatio) {
                    $pdf->setCellHeightRatio($tempCellHeightRatio);
                }
            } else {
                $tempCellHeightRatio = null;
                if ($lineSpacing) {
                    $tempCellHeightRatio = $pdf->getCellHeightRatio();
                    $pdf->setCellHeightRatio($lineSpacing);
                }

                $autopadding = false;
                $cellpadding = ['T' => 0, 'R' => 0, 'B' => 0, 'L' => 0];
                $border = 0;
                $reseth = false;
                $tempFontSize = null;
                if ($fontSize) {
                    $tempFontSize = $pdf->getFontSize();
                    $pdf->setFontSize($fontSize);
                }
                $multiCellHeight = $pdf->getStringHeight(
                    $w,
                    CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $arraydata['pattern']),
                    $reseth,
                    $autopadding,
                    $cellpadding,
                    $border
                );
                if ($tempFontSize) {
                    $pdf->setFontSize($tempFontSize);
                }
                if ($tempCellHeightRatio) {
                    $pdf->setCellHeightRatio($tempCellHeightRatio);
                }
            }
            $pdf->StopTransform();
        }

        return $multiCellHeight;
    }

    public function multiCell($arraydata) {

        //if($fielddata==true) {
        $this->checkoverflow($arraydata, $arraydata['txt'], null);
        //}
    }

    public function setXY($arraydata) {
        CReport_Jasper_Instructions::$objOutPut->SetXY($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis);
    }

    public function cell($arraydata) {
        //                print_r($arraydata);
        //              echo "<br/>";
        //JasperPHP\Pdf::$pdfOutPut->Cell($arraydata["width"], $arraydata["height"], $this->jasperReport->updatePageNo($arraydata["txt"]), $arraydata["border"], $arraydata["ln"], $arraydata["align"], $arraydata["fill"], $arraydata["link"] . "", 0, true, "T", $arraydata["valign"]);
    }

    public function rect($arraydata) {
        if ($arraydata['mode'] == 'Transparent') {
            $style = '';
        } else {
            $style = 'FD';
        }
        //      JasperPHP\Pdf::$pdfOutPut->SetLineStyle($arraydata['border']);
        CReport_Jasper_Instructions::$objOutPut->Rect($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], $style, $arraydata['border'], $arraydata['fillcolor']);
    }

    public function roundedRect($arraydata) {
        if ($arraydata['mode'] == 'Transparent') {
            $style = '';
        } else {
            $style = 'FD';
        }
        //
        //        JasperPHP\Pdf::$pdfOutPut->SetLineStyle($arraydata['border']);
        CReport_Jasper_Instructions::$objOutPut->RoundedRect($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], $arraydata['radius'], '1111', $style, $arraydata['border'], $arraydata['fillcolor']);
        //draw only border
        if (isset($arraydata['border']['width']) && $arraydata['border']['width'] > 0) {
            CReport_Jasper_Instructions::$objOutPut->SetLineStyle($arraydata['border']);
            CReport_Jasper_Instructions::$objOutPut->RoundedRect($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], $arraydata['radius'], '1111', $style, $arraydata['border']);
            CReport_Jasper_Instructions::$objOutPut->SetLineStyle([]);
        }
    }

    public function ellipse($arraydata) {
        //JasperPHP\Pdf::$pdfOutPut->SetLineStyle($arraydata['border']);
        CReport_Jasper_Instructions::$objOutPut->Ellipse($arraydata['x'] + $arraydata['width'] / 2 + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis + $arraydata['height'] / 2, $arraydata['width'] / 2, $arraydata['height'] / 2, 0, 0, 360, 'FD', $arraydata['border'], $arraydata['fillcolor']);
    }

    public function image($arraydata) {
        //echo $arraydata["path"];
        $path = $arraydata['path'];
        $imgtype = mb_substr($path, -3);
        $arraydata['link'] = $arraydata['link'] . '';
        if ($imgtype == 'jpg') {
            $imgtype = 'JPEG';
        } elseif ($imgtype == 'png' || $imgtype == 'PNG') {
            $imgtype = 'PNG';
        }
        // echo $path;
        $imagePath = str_replace(['"', '\\', '/'], ['', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR], $path);
        //not full patch?
        if (!file_exists($imagePath)) {
            $imagePath = getcwd() . DIRECTORY_SEPARATOR . $imagePath;
        }
        if (file_exists($imagePath)) {
            CReport_Jasper_Instructions::$objOutPut->Image($imagePath, $arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], $imgtype, $arraydata['link'], '', false, 300, '', false, false, $arraydata['border'], $arraydata['fitbox']);
        } elseif (mb_substr($path, 0, 4) == 'http') {
            CReport_Jasper_Instructions::$objOutPut->Image($path, $arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], $imgtype, $arraydata['link'], '', false, 300, '', false, false, $arraydata['border'], $arraydata['fitbox']);
        } elseif (mb_substr($path, 0, 21) == 'data:image/jpg;base64') {
            $imgtype = 'JPEG';
            //echo $path;
            $img = str_replace('data:image/jpg;base64,', '', $path);
            $imgdata = base64_decode($img);
            CReport_Jasper_Instructions::$objOutPut->Image('@' . $imgdata, $arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], '', '', '', false, 300, '', false, false, $arraydata['border'], $arraydata['fitbox']);
        } elseif (mb_substr($path, 0, 22) == 'data:image/png;base64,') {
            $imgtype = 'PNG';
            // JasperPHP\Pdf::$pdfOutPut->setImageScale(PDF_IMAGE_SCALE_RATIO);

            $img = str_replace('data:image/png;base64,', '', $path);
            $imgdata = base64_decode($img);

            CReport_Jasper_Instructions::$objOutPut->Image('@' . $imgdata, $arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis, $arraydata['width'], $arraydata['height'], '', $arraydata['link'], '', false, 300, '', false, false, 0, $arraydata['fitbox']);
        }
    }

    public function setTextColor($arraydata) {

        //if($this->jasperReport->hideheader==true && $this->jasperReport->currentband=='pageHeader')
        //    JasperPHP\Pdf::$pdfOutPut->SetTextColor(100,33,30);
        //else
        CReport_Jasper_Instructions::$objOutPut->SetTextColor($arraydata['r'], $arraydata['g'], $arraydata['b']);
    }

    public function setDrawColor($arraydata) {
        CReport_Jasper_Instructions::$objOutPut->SetDrawColor($arraydata['r'], $arraydata['g'], $arraydata['b']);
    }

    public function setLineWidth($arraydata) {
        CReport_Jasper_Instructions::$objOutPut->SetLineWidth($arraydata['width']);
    }

    public function breaker($arraydata) {
        $this->printExpression($arraydata);
        $pageFooter = $this->jasperReport->getRoot()->getChildByClassName('PageFooter');
        if ($this->print_expression_result == true) {
            if ($pageFooter) {
                $pageFooter->generate($this->jasperReport);
            }
            CReport_Jasper_Instructions::addInstruction(['type' => 'resetYAxis']);
            CReport_Jasper_Instructions::$currrentPage++;
            CReport_Jasper_Instructions::addInstruction(['type' => 'addPage']);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setPage', 'value' => CReport_Jasper_Instructions::$currrentPage, 'resetMargins' => false]);
            $pageHeader = $this->jasperReport->getRoot()->getChildByClassName('PageHeader');
            //if (JasperPHP\Pdf::$print_expression_result == true) {
            if ($pageHeader) {
                $pageHeader->generate($this->jasperReport);
            }
            //}
            CReport_Jasper_Instructions::runInstructions();
        }
    }

    public function line($arraydata) {
        $this->printExpression($arraydata);
        if ($this->print_expression_result == true) {
            //var_dump($arraydata["style"]);
            //echo ($arraydata["x1"] + CReport_Jasper_Instructions::$arrayPageSetting["leftMargin"])."||". ($arraydata["y1"] + CReport_Jasper_Instructions::$y_axis)."||". ($arraydata["x2"] + CReport_Jasper_Instructions::$arrayPageSetting["leftMargin"])."||". $arraydata["y2"] + CReport_Jasper_Instructions::$y_axis."||". $arraydata["style"];

            CReport_Jasper_Instructions::$objOutPut->Line((int) $arraydata['x1'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], (int) $arraydata['y1'] + CReport_Jasper_Instructions::$yAxis, (int) $arraydata['x2'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], (int) $arraydata['y2'] + CReport_Jasper_Instructions::$yAxis, $arraydata['style']);
        }
    }

    public function setFillColor($arraydata) {
        CReport_Jasper_Instructions::$objOutPut->SetFillColor($arraydata['r'], $arraydata['g'], $arraydata['b']);
    }

    public function lineChart($arraydata) {

        // $this->generateLineChart($arraydata, JasperPHP\Pdf::$y_axis);
    }

    public function barChart($arraydata) {

        // $this->generateBarChart($arraydata, JasperPHP\Pdf::$y_axis, 'barChart');
    }

    public function pieChart($arraydata) {

        //$this->generatePieChart($arraydata, JasperPHP\Pdf::$y_axis);
    }

    public function stackedBarChart($arraydata) {

        //$this->generateBarChart($arraydata, JasperPHP\Pdf::$y_axis, 'stackedBarChart');
    }

    public function stackedAreaChart($arraydata) {

        //$this->generateAreaChart($arraydata, JasperPHP\Pdf::$y_axis, $arraydata["type"]);
    }

    public function barcode($arraydata) {
        $this->showBarcode($arraydata, CReport_Jasper_Instructions::$yAxis);
    }

    public function crossTab($arraydata) {

        //$this->generateCrossTab($arraydata, JasperPHP\Pdf::$y_axis);
    }

    public function table($arraydata) {
        CReport_Jasper_Element_Table::process($arraydata);
    }

    public function showBarcode($data, $y) {
        $pdf = CReport_Jasper_Instructions::get();
        $type = strtoupper($data['barcodetype']);
        $height = $data['height'];
        $width = $data['width'];
        $x = $data['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'];
        $y = $data['y'] + $y;
        $textposition = $data['textposition'];
        $code = $data['code'];
        //$code=$this->analyse_expression($code);
        $modulewidth = $data['modulewidth'];
        if ($textposition == '' || $textposition == 'none') {
            $withtext = false;
        } else {
            $withtext = true;
        }

        $style = [
            'border' => false,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'text' => $withtext,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false, //array(255,255,255)
            'module_width' => 1, // width of a single module in points
            'module_height' => 1 // height of a single module in points
        ];

        //[2D barcode section]
        //DATAMATRIX
        //QRCODE,H or Q or M or L (H=high level correction, L=low level correction)
        // -------------------------------------------------------------------
        // PDF417 (ISO/IEC 15438:2006)

        /*

          The $type parameter can be simple 'PDF417' or 'PDF417' followed by a
          number of comma-separated options:

          'PDF417,a,e,t,s,f,o0,o1,o2,o3,o4,o5,o6'

          Possible options are:

          a  = aspect ratio (width/height);
          e  = error correction level (0-8);

          Macro Control Block options:

          t  = total number of macro segments;
          s  = macro segment index (0-99998);
          f  = file ID;
          o0 = File Name (text);
          o1 = Segment Count (numeric);
          o2 = Time Stamp (numeric);
          o3 = Sender (text);
          o4 = Addressee (text);
          o5 = File Size (numeric);
          o6 = Checksum (numeric).

          Parameters t, s and f are required for a Macro Control Block, all other parametrs are optional.
          To use a comma character ',' on text options, replace it with the character 255: "\xff".

         */
        switch ($type) {
            case 'PDF417':
                $pdf->write2DBarcode($code, 'PDF417', $x, $y, $width, $height, $style, 'N');

                break;
            case 'DATAMATRIX':
                //$this->pdf->Cell( $width,10,$code);
                //echo $this->left($code,3);
                if (cstr::substr($code, 0, 3) == 'QR:') {
                    $code = cstr::substr($code, 3);

                    $pdf->write2DBarcode($code, 'QRCODE', $x, $y, $width, $height, $style, 'N');
                } else {
                    $pdf->write2DBarcode($code, 'DATAMATRIX', $x, $y, $width, $height, $style, 'N');
                }

                break;
            case 'QRCODE':
                $pdf->write2DBarcode($code, 'QRCODE', $x, $y, $width, $height, $style, 'N');

                break;
            case 'CODE128':
                $pdf->write1DBarcode($code, 'C128', $x, $y, $width, $height, $modulewidth, $style, 'N');

                // $this->pdf->write1DBarcode($code, 'C128', $x, $y, $width, $height,"", $style, 'N');
                break;
            case 'EAN8':
                $pdf->write1DBarcode($code, 'EAN8', $x, $y, $width, $height, $modulewidth, $style, 'N');

                break;
            case 'EAN13':
                $pdf->write1DBarcode($code, 'EAN13', $x, $y, $width, $height, $modulewidth, $style, 'N');

                break;
            case 'CODE39':
                $pdf->write1DBarcode($code, 'C39', $x, $y, $width, $height, $modulewidth, $style, 'N');

                break;
            case 'CODE93':
                $pdf->write1DBarcode($code, 'C93', $x, $y, $width, $height, $modulewidth, $style, 'N');

                break;
            case 'I25':
            case 'INT2OF5':
            case 'INTERLEAVED2OF5':
                $pdf->write1DBarcode($code, 'I25', $x, $y, $width, $height, $modulewidth, $style, 'N');

                break;
            case 'POSTNET':
                $pdf->write1DBarcode($code, 'POSTNET', $x, $y, $width, $height, $modulewidth, $style, 'N');

                break;
        }
    }

    public function checkoverflow($obj) {
        /** @var \TCPDF $pdf */
        $pdf = CReport_Jasper_Instructions::$objOutPut;
        $JasperObj = $this->jasperReport;
        // var_dump($obj->children);
        $txt = (string) $obj['txt'];
        //$newfont = $JasperObj->recommendFont($txt, null, null);
        //$pdf->SetFont($newfont,$pdf->getFontStyle(),$this->defaultFontSize);
        $this->printExpression($obj);
        // if ($obj['printWhenExpression']) {
        //     $text = $obj['txt'];
        //     if (cstr::startsWith($text, 'Closing')) {
        //         // if ($this->print_expression_result) {

        //         cdbg::dd($obj['printWhenExpression'], $JasperObj->arrayVariable, $obj, $this->print_expression_result);
        //         // }
        //     }
        // }
        $arraydata = $obj;

        $pdf->SetXY($arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'], $arraydata['y'] + CReport_Jasper_Instructions::$yAxis);
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        //default
        $pLeft = 1;
        $pTop = 0;
        $pRight = 1;
        $pBottom = 0;
        //suport padding cells
        if (isset($obj['box']) && !empty($obj['box'])) {
            if (isset($obj['box']['leftPadding'])) {
                $pLeft = $obj['box']['leftPadding'];
            }
            if (isset($obj['box']['topPadding'])) {
                $pTop = $obj['box']['topPadding'];
            }
            if (isset($obj['box']['rightPadding'])) {
                $pRight = $obj['box']['rightPadding'];
            }
            if (isset($obj['box']['bottomPadding'])) {
                $pBottom = $obj['box']['bottomPadding'];
            }
        }
        $pdf->setCellPaddings($pLeft, $pTop, $pRight, $pBottom);
        $w = $arraydata['width'];
        $h = $arraydata['height'];
        $pdf->StartTransform();

        $clipx = $arraydata['x'] + CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'];
        $clipy = $arraydata['y'] + CReport_Jasper_Instructions::$yAxis;
        $clipw = $arraydata['width'];
        $cliph = $arraydata['height'];

        $rotated = false;
        if ($this->print_expression_result == true) {
            $angle = $this->rotate($arraydata);
            if ($angle != 0) {
                $pdf->Rect($clipx, $clipy, $clipw, $cliph, 'CNZ');
                $pdf->Rotate($angle);
                $rotated = true;
                switch ($angle) {
                    case 90:
                        $x = $x - $arraydata['height'];
                        $h = $arraydata['width'];
                        $w = $arraydata['height'];

                        break;
                    case 180:
                        $x = $x - $arraydata['width'];
                        $y = $y - $arraydata['height'];

                        break;
                    case 270:
                        $y = $y - $arraydata['width'];
                        $h = $arraydata['width'];
                        $w = $arraydata['height'];

                        break;
                }
            }
            // echo $arraydata["link"];
            if ($arraydata['link']) {
                //print_r($arraydata);
                //$this->debughyperlink=true;
                //  echo $arraydata["link"].",print:".$this->print_expression_result;
                //$arraydata["link"] = $JasperObj->analyse_expression($arraydata["link"], "");
                //$this->debughyperlink=false;
            }
            //print_r($arraydata);

            if ($arraydata['writeHTML'] == true) {
                //echo  ($txt);
                $pdf->writeHTML($txt, true, 0, true, true);
                $pdf->Ln();
            } elseif ($arraydata['poverflow'] == 'false' && $arraydata['soverflow'] == 'false') {
                if ($arraydata['valign'] == 'C') {
                    $arraydata['valign'] = 'M';
                }
                if ($arraydata['valign'] == '') {
                    $arraydata['valign'] = 'T';
                }

                // clip width & height
                if (!$rotated) {
                    $pdf->Rect($clipx, $clipy, $clipw, $cliph, 'CNZ');
                }

                $pattern = (array_key_exists('pattern', $arraydata)) ? $arraydata['pattern'] : '';
                $text = $pattern != '' ? CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $pattern) : $txt;
                $pdf->MultiCell(
                    $w,
                    $h,
                    $text,
                    $arraydata['border'],
                    $arraydata['align'],
                    $arraydata['fill'],
                    0,
                    $x,
                    $y,
                    true,
                    0,
                    false,
                    true,
                    $h,
                    $arraydata['valign']
                );
                if (isset($arraydata['link']) && !empty($arraydata['link'])) {
                    $pdf->Link($x, $y, $arraydata['width'], $arraydata['height'], $arraydata['link']);
                }
            } elseif ($arraydata['poverflow'] == 'true' || $arraydata['soverflow'] == 'true') {
                if ($arraydata['valign'] == 'C') {
                    $arraydata['valign'] = 'M';
                }
                if ($arraydata['valign'] == '') {
                    $arraydata['valign'] = 'T';
                }

                $x = $pdf->GetX();
                $yAfter = $pdf->GetY();
                $maxheight = array_key_exists('maxheight', $arraydata) ? $arraydata['maxheight'] : 0;
                //if($arraydata["link"])   echo $arraydata["linktarget"].",".$arraydata["link"]."<br/><br/>";
                $pdf->MultiCell($w, $h, CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $arraydata['pattern']), $arraydata['border'], $arraydata['align'], $arraydata['fill'], 1, $x, $y, true, 0, false, true, $maxheight); //,$arraydata["valign"]);
                if (($yAfter + $arraydata['height']) <= CReport_Jasper_Instructions::$arrayPageSetting['pageHeight']) {
                    CReport_Jasper_Instructions::$yAxis = $pdf->GetY() - 20;
                }
            } else {
                $pdf->MultiCell($w, $h, CReport_Jasper_Utils_FormatUtils::formatPattern($txt, $arraydata['pattern']), $arraydata['border'], $arraydata['align'], $arraydata['fill'], 1, $x, $y, true, 0, true, true);
            }
            $pdf->StopTransform();
        }
    }

    public function printExpression($data) {
        $expression = $data['printWhenExpression'];
        $this->print_expression_result = false;
        if ($expression != '') {
            //echo      'if('.$expression.'){$this->print_expression_result=true;}';
            //$expression=$this->analyse_expression($expression);
            error_reporting(0);
            eval('if(' . $expression . '){$this->print_expression_result=true;}');
            error_reporting(5);
        } else {
            $this->print_expression_result = true;
        }
    }

    public function rotate($arraydata) {
        $pdf = CReport_Jasper_Instructions::$objOutPut;
        if (array_key_exists('rotation', $arraydata)) {
            $type = (string) $arraydata['rotation'];
            $angle = null;
            if ($type == '') {
                $angle = 0;
            } elseif ($type == 'Left') {
                $angle = 90;
            } elseif ($type == 'Right') {
                $angle = 270;
            } elseif ($type == 'UpsideDown') {
                $angle = 180;
            }

            return $angle;
        }
    }
}

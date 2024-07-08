<?php

class CReport_Jasper_Element_TextField extends CReport_Jasper_Element {
    protected $forceHeight;

    public function getHeight() {
        $height = (float) $this->xmlElement->reportElement['height'];

        return $height;
    }

    protected function getLineSpacing() {
        $lineSpacing = CReport_Jasper_Utils_ElementUtils::getLineHeightRatio($this->xmlElement->textElement, 1);

        return $lineSpacing;
    }

    protected function getFontSize() {
        $fontSize = null;
        if (isset($this->xmlElement->textElement, $this->xmlElement->textElement->font['size'])) {
            $fontSize = (float) $this->xmlElement->textElement->font['size'];
        }

        return $fontSize;
    }

    public function forceHeight($height) {
        $this->forceHeight = $height;

        return $this;
    }

    // public function calculateHeight() {
    //     $generator = $this->getGenerator();
    //     if ($generator) {
    //         $processor = $generator->getReport()->getProcessor();

    //         return $processor->getHeightMultiCell()
    //     }
    // }

    public function getInstructionDataMultiCell(CReport_Jasper_Report $report) {
        $rowData = $report->getCurrentRow();

        $data = $this->xmlElement;
        /** @var CReport_Jasper_Report $obj */
        $border = 0;
        $align = 'L';
        $fill = 0;
        $fontsize = 10;
        $height = $data->reportElement['height'];
        $stretchoverflow = 'false';
        $printoverflow = 'false';
        $rotation = '';
        $valign = '';
        $writeHTML = '';
        $box = [];
        $multiCell = false;
        $isPrintRepeatedValues = '';
        $text = (string) $data->textFieldExpression;
        $arrayText = explode('+', $text);
        if ($data->reportElement['mode'] == 'Opaque') {
            $fill = 1;
        }

        $hyperlinkReferenceExpression = $data->hyperlinkReferenceExpression;
        if ($hyperlinkReferenceExpression) {
            $hyperlinkReferenceExpression = $report->getExpression($hyperlinkReferenceExpression, $rowData, false, $this);
        }

        if ($this->getProperty('textAdjust') == 'StretchHeight' || $this->getProperty('isStretchWithOverflow') == 'true') {
            $stretchoverflow = 'true';
        }
        if (isset($data->reportElement['isPrintWhenDetailOverflows']) && $data->reportElement['isPrintWhenDetailOverflows'] == 'true') {
            $printoverflow = 'true';
        }

        if (isset($data->textElement['textAlignment'])) {
            $align = $this->getFirstValue($data->textElement['textAlignment']);
        }
        if (isset($data->textElement['verticalAlignment'])) {
            $valign = 'T';
            if ($data->textElement['verticalAlignment'] == 'Bottom') {
                $valign = 'B';
            } elseif ($data->textElement['verticalAlignment'] == 'Middle') {
                $valign = 'C';
            } else {
                $valign = 'T';
            }
        }
        if (isset($data->textElement['rotation'])) {
            $rotation = $data->textElement['rotation'];
        }
        if (isset($data->box)) {
            $border = CReport_Jasper_Utils_ElementUtils::formatBorder($data->box);
            $box = CReport_Jasper_Utils_ElementUtils::formatBox($data->box);
        }
        if (isset($data->reportElement['key']) && !empty($data->reportElement['key'])) {
            $height = $fontsize;
        }
        $printWhenExpression = '';
        if ($data->reportElement->printWhenExpression) {
            $printWhenExpression = $report->getExpression($data->reportElement->printWhenExpression, $rowData);
        }

        if ($data->textFieldExpression == 'new java.util.Date()') {
            $text = date('Y-m-d H:i:s');
        } else {
            $text = $report->getExpression($text, $rowData, $writeHTML, $this);
        }
        if ($printoverflow == 'true' || $stretchoverflow == 'true') {
            $text = str_ireplace(['+', '+', '"'], ['', '', ''], $text);
        }

        $patternExpression = $this->xmlElement->patternExpression;
        $writeHTML = false;

        if ($data->textElement['markup'] == 'html') {
            $writeHTML = true;
        }

        $writeHTML = false;
        if ($data->textElement['markup'] == 'html') {
            $writeHTML = 1;
        } elseif ($data->textElement['markup'] == 'rtf') {
            $multiCell = true;
        } else {
            $text = str_ireplace(['"+', '" +', '+"', '+ "', '"', '\n'], ['', '', ''], $text);
        }
        if (isset($data->reportElement['isPrintRepeatedValues'])) {
            $isPrintRepeatedValues = $data->reportElement['isPrintRepeatedValues'];
        }
        if ($this->forceHeight !== null) {
            $height = $this->forceHeight;
            $stretchoverflow = 'false';
        }
        // if ($printWhenExpression) {
        //     if (cstr::startsWith($text, 'Closing')) {
        //         // if ($this->print_expression_result) {

        //         cdbg::dd($data->reportElement->printWhenExpression, $printWhenExpression, $report->arrayVariable, $obj);
        //         // }
        //     }
        // }
        $result = ['type' => 'multiCell', 'width' => $data->reportElement['width'] + 0, 'height' => $height + 0,
            'txt' => $text . '',
            'border' => $border, 'align' => $align, 'fill' => $fill,
            'hidden_type' => 'field', 'soverflow' => $stretchoverflow, 'poverflow' => $printoverflow,
            'printWhenExpression' => $printWhenExpression . '',
            'link' => $hyperlinkReferenceExpression . '',
            'pattern' => $data->pattern,
            'linktarget' => $data['hyperlinkTarget'] . '',
            'writeHTML' => $writeHTML,
            'multiCell' => $multiCell,
            'fontSize' => $this->getFontSize(),
            'lineSpacing' => $this->getLineSpacing(),
            'isPrintRepeatedValues' => $isPrintRepeatedValues,
            'rotation' => $rotation,
            'valign' => $valign,
            'box' => $box,
            'x' => $data->reportElement['x'] + 0, 'y' => $data->reportElement['y'] + 0
        ];

        return $result;
    }

    public function generate(CReport_Jasper_Report $report) {
        $orginalObj = $report;
        $data = $this->xmlElement;
        $rowData = $report->getCurrentRow();

        $font = 'helvetica';
        $fill = 0;
        $fontstyle = '';
        $textcolor = ['r' => 0, 'g' => 0, 'b' => 0];
        $fillcolor = ['r' => 255, 'g' => 255, 'b' => 255];

        $drawcolor = ['r' => 0, 'g' => 0, 'b' => 0];

        //SimpleXML object (1 item) [0] // ->codeExpression[0] ->attributes('xsi', true) ->schemaLocation ->attributes('', true) ->type ->drawText ->checksumRequired barbecue:
        //SimpleXMLElement Object ( [@attributes] => Array ( [hyperlinkType] => Reference [hyperlinkTarget] => Blank ) [reportElement] => SimpleX
        //print_r( $data["@attributes"]);

        //apply style formatting
        if (isset($data->reportElement['style'])) {
            $name = $data->reportElement['style'];
            $report->applyStyle($name, $data->reportElement, $rowData);
        }

        if (isset($data->reportElement['forecolor'])) {
            $textcolor = [
                'r' => hexdec(substr($data->reportElement['forecolor'], 1, 2)),
                'g' => hexdec(substr($data->reportElement['forecolor'], 3, 2)),
                'b' => hexdec(substr($data->reportElement['forecolor'], 5, 2))
            ];
        }
        if (isset($data->reportElement['backcolor'])) {
            $fillcolor = [
                'r' => hexdec(substr($data->reportElement['backcolor'], 1, 2)),
                'g' => hexdec(substr($data->reportElement['backcolor'], 3, 2)),
                'b' => hexdec(substr($data->reportElement['backcolor'], 5, 2))
            ];
        }

        if (isset($data->textElement->font['fontName'])) {
            //   $font=$this->recommendFont($data->textFieldExpression,$data->textElement->font["fontName"],$data->textElement->font["pdfFontName"]);
            //$data->textFieldExpression=$font;//$data->textElement->font["pdfFontName"];
            $font = $data->textElement->font['fontName'];
        }
        if (isset($data->textElement->font['size'])) {
            $fontsize = $data->textElement->font['size'];
        }
        if (isset($data->textElement->font['isBold']) && $data->textElement->font['isBold'] == 'true') {
            $fontstyle = $fontstyle . 'B';
        }
        if (isset($data->textElement->font['isItalic']) && $data->textElement->font['isItalic'] == 'true') {
            $fontstyle = $fontstyle . 'I';
        }
        if (isset($data->textElement->font['isUnderline']) && $data->textElement->font['isUnderline'] == 'true') {
            $fontstyle = $fontstyle . 'U';
        }
        if ($data->reportElement['mode'] == 'Opaque') {
            $fill = 1;
        }
        $lineHeightRatio = CReport_Jasper_Utils_ElementUtils::getLineHeightRatio($data->textElement, 1);

        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setCellHeightRatio',
            'ratio' => $lineHeightRatio
        ]);

        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setXY',
            'x' => $data->reportElement['x'] + 0,
            'y' => $data->reportElement['y'] + 0,
            'hidden_type' => 'setXY'
        ]);
        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setTextColor',
            'forecolor' => $data->reportElement['forecolor'],
            'r' => $textcolor['r'],
            'g' => $textcolor['g'],
            'b' => $textcolor['b'],
            'hidden_type' => 'textcolor'
        ]);
        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setDrawColor',
            'r' => $drawcolor['r'],
            'g' => $drawcolor['g'],
            'b' => $drawcolor['b'],
            'hidden_type' => 'drawcolor'
        ]);
        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setFillColor',
            'backcolor' => $data->reportElement['backcolor'] . '',
            'r' => $fillcolor['r'],
            'g' => $fillcolor['g'],
            'b' => $fillcolor['b'],
            'hidden_type' => 'fillcolor',
            'fill' => $fill
        ]);
        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setFont',
            'font' => $font . '',
            'pdfFontName' => $data->textElement->font ? $data->textElement->font['pdfFontName'] . '' : '',
            'fontstyle' => $fontstyle . '',
            'fontsize' => $fontsize + 0,
            'hidden_type' => 'font'
        ]);

        CReport_Jasper_Instructions::addInstruction($this->getInstructionDataMultiCell($orginalObj));

        //$this->checkoverflow($pointer);

        parent::generate($report);
    }
}

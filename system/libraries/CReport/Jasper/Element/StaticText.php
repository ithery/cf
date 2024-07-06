<?php

class CReport_Jasper_Element_StaticText extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $row = $report->getCurrentRow();
        $data = $this->xmlElement;
        $align = 'L';
        $fill = 0;
        $border = 0;
        $fontsize = 10;
        $font = 'helvetica';
        $fontstyle = '';
        $textcolor = ['r' => 0, 'g' => 0, 'b' => 0];
        $fillcolor = ['r' => 255, 'g' => 255, 'b' => 255];
        $txt = '';
        $rotation = '';
        $drawcolor = ['r' => 0, 'g' => 0, 'b' => 0];
        $height = $data->reportElement['height'];
        $stretchoverflow = 'false';
        $printoverflow = 'false';
        $writeHTML = false;
        $isPrintRepeatedValues = '';
        $valign = '';
        //$data->hyperlinkReferenceExpression=$this->analyse_expression($data->hyperlinkReferenceExpression);
        $data->hyperlinkReferenceExpression = trim(str_replace([' ', '"'], '', $data->hyperlinkReferenceExpression));
        if (isset($data->reportElement['forecolor'])) {
            $textcolor = ['forecolor' => $data->reportElement['forecolor'], 'r' => hexdec(substr($data->reportElement['forecolor'], 1, 2)), 'g' => hexdec(substr($data->reportElement['forecolor'], 3, 2)), 'b' => hexdec(substr($data->reportElement['forecolor'], 5, 2))];
        }
        if (isset($data->reportElement['backcolor'])) {
            $fillcolor = ['backcolor' => $data->reportElement['backcolor'], 'r' => hexdec(substr($data->reportElement['backcolor'], 1, 2)), 'g' => hexdec(substr($data->reportElement['backcolor'], 3, 2)), 'b' => hexdec(substr($data->reportElement['backcolor'], 5, 2))];
        }
        if ($data->reportElement['mode'] == 'Opaque') {
            $fill = 1;
        }
        if (isset($data['isStretchWithOverflow']) && $data['isStretchWithOverflow'] == 'true') {
            $stretchoverflow = 'true';
        }
        if (isset($data->reportElement['isPrintWhenDetailOverflows']) && $data->reportElement['isPrintWhenDetailOverflows'] == 'true') {
            $printoverflow = 'true';
            $stretchoverflow = 'false';
        }
        $box = [];
        if (isset($data->box)) {
            $border = CReport_Jasper_Utils_ElementUtils::formatBorder($data->box);
            $box = CReport_Jasper_Utils_ElementUtils::formatBox($data->box);
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
        if (isset($data->textElement->font['fontName'])) {
            //else
            //$data->text=$data->textElement->font["pdfFontName"];//$this->recommendFont($data->text);
            $font = $this->recommendFont($data->text, $data->textElement->font['fontName'], $data->textElement->font['pdfFontName']);
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
        // if (isset($data->reportElement['key']) && !empty($data->reportElement['key'])) {
        //     $height = $fontsize * $this->adjust;
        // }
        $lineHeightRatio = CReport_Jasper_Utils_ElementUtils::getLineHeightRatio($data->textElement, 1);

        CReport_Jasper_Instructions::addInstruction([
            'type' => 'setCellHeightRatio',
            'ratio' => $lineHeightRatio
        ]);
        CReport_Jasper_Instructions::addInstruction(['type' => 'setXY', 'x' => $data->reportElement['x'] + 0, 'y' => $data->reportElement['y'] + 0, 'hidden_type' => 'SetXY']);
        CReport_Jasper_Instructions::addInstruction(['type' => 'setTextColor', 'forecolor' => $data->reportElement['forecolor'] . '', 'r' => $textcolor['r'], 'g' => $textcolor['g'], 'b' => $textcolor['b'], 'hidden_type' => 'textcolor']);
        CReport_Jasper_Instructions::addInstruction(['type' => 'setDrawColor', 'r' => $drawcolor['r'], 'g' => $drawcolor['g'], 'b' => $drawcolor['b'], 'hidden_type' => 'drawcolor']);
        CReport_Jasper_Instructions::addInstruction(['type' => 'setFillColor', 'backcolor' => $data->reportElement['backcolor'] . '', 'r' => $fillcolor['r'], 'g' => $fillcolor['g'], 'b' => $fillcolor['b'], 'hidden_type' => 'fillcolor']);
        CReport_Jasper_Instructions::addInstruction(['type' => 'setFont', 'font' => $font, 'pdfFontName' => $data->textElement->font ? $data->textElement->font['pdfFontName'] : '', 'fontstyle' => $fontstyle, 'fontsize' => $fontsize, 'hidden_type' => 'font']);
        //"height"=>$data->reportElement["height"]
        //### UTF-8 characters, a must for me.
        $txtEnc = $data->text;

        $print_expression_result = false;
        $printWhenExpression = (string) $data->reportElement->printWhenExpression;
        if ($printWhenExpression != '') {
            $printWhenExpression = $report->getExpression($printWhenExpression, $row);

            //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
        } else {
            $print_expression_result = true;
        }
        if ($print_expression_result == true) {
            CReport_Jasper_Instructions::addInstruction(['type' => 'multiCell', 'width' => $data->reportElement['width'], 'height' => $height,
                'txt' => $txtEnc, 'border' => $border, 'align' => $align, 'fill' => $fill, 'hidden_type' => 'statictext',
                'printWhenExpression' => $printWhenExpression . '',
                'multiCell' => false,
                'soverflow' => $stretchoverflow, 'poverflow' => $printoverflow, 'rotation' => $rotation, 'valign' => $valign, 'link' => null,
                'x' => $data->reportElement['x'] + 0, 'y' => $data->reportElement['y'] + 0,
                'box' => $box,
                'writeHTML' => $writeHTML]);
        }
        //### End of modification, below is the original line
        //        $pointer=array("type"=>"MultiCell","width"=>$data->reportElement["width"],"height"=>$height,"txt"=>$data->text,"border"=>$border,"align"=>$align,"fill"=>$fill,"hidden_type"=>"statictext","soverflow"=>$stretchoverflow,"poverflow"=>$printoverflow,"rotation"=>$rotation);
        //$this->checkoverflow($pointer);
    }
}

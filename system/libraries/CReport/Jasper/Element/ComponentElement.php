<?php

class CReport_Jasper_Element_ComponentElement extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $data = $this->xmlElement;
        $rowData = $report->getCurrentRow();
        $x = $data->reportElement['x'];
        $y = $data->reportElement['y'];
        $width = $data->reportElement['width'];
        $height = $data->reportElement['height'];

        //table =========================================
        $jrs = $data->children('jr', true);
        if (isset($jrs->table)) {
            $table = new CReport_Jasper_Element_Table($jrs->table);
            $table->setReportElement($data->reportElement);
            $table->generate($report);
        }//end table

        //simplexml_tree( $data);
        // echo "<br/><br/>";
        //simplexml_tree( $data->children('jr',true));
        //echo "<br/><br/>";
        //SimpleXML object (1 item) [0] // ->codeExpression[0] ->attributes('xsi', true) ->schemaLocation ->attributes('', true) ->type ->drawText ->checksumRequired barbecue:
        foreach ($data->children('jr', true) as $barcodetype => $content) {
            $text = $report->getExpression($content->codeExpression, $rowData, false, $this);

            $barcodemethod = '';
            $textposition = '';
            if ($barcodetype == 'barbecue') {
                $barcodemethod = $data->children('jr', true)->attributes('', true)->type;
                //var_dump($barcodemethod);
                $textposition = '';
                $checksum = $data->children('jr', true)->attributes('', true)->checksumRequired;
                $code = $text;
                if ($content->attributes('', true)->drawText == 'true') {
                    $textposition = 'bottom';
                }

                $modulewidth = $content->attributes('', true)->moduleWidth;
            } else {
                $barcodemethod = $barcodetype;
                $textposition = $content->attributes('', true)->textPosition;
                //$data->children('jr',true)->textPosition;
                //$content['textPosition'];
                $code = $text;
                $modulewidth = $content->attributes('', true)->moduleWidth;
            }
            if ($modulewidth == '') {
                $modulewidth = 1;
            }
            //                            echo "Barcode: $code,position: $textposition <br/><br/>";
            CReport_Jasper_Instructions::addInstruction(['type' => 'Barcode', 'barcodetype' => $barcodemethod, 'x' => $x, 'y' => $y, 'width' => $width, 'height' => $height, 'textposition' => $textposition, 'code' => $code, 'modulewidth' => $modulewidth]);
        }
    }
}

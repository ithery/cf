<?php

class CReport_Jasper_Element_Table extends CReport_Jasper_Element {
    private $arrayVariable;

    private static $page = 0;

    private $reportElement = null;

    public function setReportElement($reportElement) {
        $this->reportElement = $reportElement;
    }

    public function getColorFill($data) {
        if (isset($data['backcolor'])) {
            return ['backcolor' => $data['backcolor'], 'r' => hexdec(substr($data['backcolor'], 1, 2)), 'g' => hexdec(substr($data['backcolor'], 3, 2)), 'b' => hexdec(substr($data['backcolor'], 5, 2))];
        }
    }

    public function prepareColumn($column, $obj) {
        $objColumn = [];
        $attributes = $column->attributes();
        $borders = '';
        $box = [];
        //border definition style
        if (isset($attributes['style'])) {
            $style = $obj->getStyle($attributes['style']);
            $box = $style->box;
            $att = $style->attributes();//[mode] => Opaque [backcolor] => #BFE1FF
            if ($att['mode'] == 'Opaque') {
                $objColumn['fill'] = 1;
                $objColumn['fillcolor'] = $this->getColorFill($att);
            }
        }
        //border cell definition
        if ($column->children()->box) {
            $box = (object) array_merge((array) $box, (array) $column->children()->box);
        }
        $borders = CReport_Jasper_Utils_ElementUtils::formatBox($box);
        $objColumn['borders'] = $borders;//default
        $objColumn['h'] = $attributes['height'];
        foreach ($column->children() as $k => $v) {
            $className = 'JasperPHP\\' . ucfirst($k);
            //echo $className."|";
            if (class_exists($className)) {
                $objColumn['field'] = new $className($v);
            }
        }

        return $objColumn;
    }

    public function variableHandler($xml_variables) {
        $this->arrayVariable = [];
        foreach ($xml_variables as $variable) {
            $varName = (string) $variable['name'];
            $this->arrayVariable[$varName] = ['calculation' => $variable['calculation'] . '',
                'target' => $variable->variableExpression,
                'class' => $variable['class'] . '',
                'resetType' => $variable['resetType'] . '',
                'resetGroup' => $variable['resetGroup'] . '',
                'initialValue' => (string) $variable->initialValueExpression . '',
                'incrementType' => $variable['incrementType']
            ];
        }

        return $this->arrayVariable;
    }

    public function generate($obj = null) {
        $data = $this->xmlElement;
        //ComponentElement
        $reportElement = $obj[2];
        $rowData = is_array($obj) ? $obj[1] : null;
        $obj = is_array($obj) ? $obj[0] : $obj;
        $x = $reportElement['x'];
        $y = $reportElement['y'];
        $width = $reportElement['width'];
        $height = $reportElement['height'];
        $variables = [];
        $borders = 'LRBT';//default

        $dataRowTable = [];
        $table = $data;
        $datasetRun = $table->children();
        $subDataset_name = trim($datasetRun->datasetRun->attributes()['subDataset']);
        //subDataset
        foreach ($obj->xmlElement->subDataset as $dataSet) {
            $name = trim($dataSet->attributes()['name']);
            //is dataSet of table?
            if ($name == $subDataset_name) {
                //get variables dataSet
                $obj->arrayVariable = $this->variableHandler($dataSet->variable);
                //prepare newParameters and send query table
                if (is_array($rowData)) {
                    $rowArray = $rowData;
                } elseif (is_object($rowData)) {
                    if (method_exists($rowData, 'toArray')) {
                        $rowArray = $rowData->toArray();
                    } else {
                        $rowArray = get_object_vars($rowData);
                    }
                }
                $newParameters = ($rowArray) ? array_merge($obj->arrayParameter, $rowArray) : $obj->arrayParameter;
                //print_r($newParameters);
                $sql = trim($dataSet->queryString);
                $sql = $obj->prepareSql($sql, $newParameters);
                $dataRowTable = $obj->getDbDataQuery($sql);
            }
        }
        //get all columns
        $columns = [];
        $i = 0;
        foreach ($table->column as $key => $column) {
            $objColumn = [];
            $objColumn['w'] = $column->attributes()['width'];
            //prepare columns bands
            if (isset($column->tableHeader)) {
                $objColumn['tableHeader'] = $this->prepareColumn($column->tableHeader, $obj);
            }
            if (isset($column->columnHeader)) {
                $objColumn['columnHeader'] = $this->prepareColumn($column->columnHeader, $obj);
            }
            if (isset($column->detailCell)) {
                $objColumn['detailCell'] = $this->prepareColumn($column->detailCell, $obj);
            }
            if (isset($column->columnFooter)) {
                $objColumn['columnFooter'] = $this->prepareColumn($column->columnFooter, $obj);
            }
            if (isset($column->tableFooter)) {
                $objColumn['tableFooter'] = $this->prepareColumn($column->tableFooter, $obj);
            }
            $columns[$i] = $objColumn;
            $i++;
        }//end each column
        CReport_Jasper_Instructions::addInstruction(['type' => 'Table', 'obj' => $obj, 'x' => $x, 'y' => $y, 'column' => $columns, 'data' => $dataRowTable]);
    }

    public static function process($arraydata) {
        $jasperObj = $arraydata['obj'];
        $pdf = CReport_Jasper_Instructions::get();
        $dimensions = $pdf->getPageDimensions();
        $topMargin = CReport_Jasper_Instructions::$arrayPageSetting['topMargin'];
        $dbData = $arraydata['data'];
        $columns = $arraydata['column'];
        $pdf->Ln(0);
        self::setYAxis($arraydata['y']);

        $showColumnHeader = true;
        //after font definition
        $fontDefault = [];
        $fontDefault['font'] = $pdf->getFontFamily();
        $fontDefault['fontstyle'] = $pdf->getFontStyle();
        $fontDefault['fontsize'] = $pdf->getFontSize();

        $totalRows = is_array($dbData) ? count($dbData) : $dbData->rowCount();

        //each row data
        $rowIndex = 0;
        foreach ($dbData as $row) {
            self::$page = CReport_Jasper_Instructions::$currrentPage;
            $borders = 'LRBT';//default
            $rowIndex++;
            //variables dataset================================
            $jasperObj->arrayVariable['REPORT_COUNT']['ans'] = $rowIndex;
            $jasperObj->arrayVariable['REPORT_COUNT']['target'] = $rowIndex;
            $jasperObj->arrayVariable['REPORT_COUNT']['calculation'] = null;
            $jasperObj->arrayVariable['totalRows']['ans'] = $totalRows;
            $jasperObj->arrayVariable['totalRows']['target'] = $totalRows;
            $jasperObj->arrayVariable['totalRows']['calculation'] = null;
            $jasperObj->totalRows = $totalRows;
            $jasperObj->variables_calculation($jasperObj, $row);
            //endVariables

            $marginLeft = CReport_Jasper_Instructions::$arrayPageSetting['leftMargin'];
            //get height header and detail
            $height_header = 0;
            $height_detail = 0;
            $height_columnFooter = 0;
            $height_tableFooter = 0;
            foreach ($arraydata['column'] as $k => $column) {
                $width_column = $column['w'];

                if ($column['columnFooter']['h'] > $height_columnFooter) {
                    $height_columnFooter = $column['columnFooter']['h'];
                }
                if ($column['tableFooter']['h'] > $height_tableFooter) {
                    $height_tableFooter = $column['tableFooter']['h'];
                }

                //height header default =================================
                if ($column['columnHeader']['h'] > $height_header) {
                    $height_header = $column['columnHeader']['h'];
                }
                //get max height
                if (isset($column['columnHeader']['field'])) {
                    $field = $column['columnHeader']['field'];
                    $text = $jasperObj->get_expression($field->xmlElement->text, $row);
                    //change font for height row
                    $font = $field->xmlElement->textElement->font->attributes();
                    CReport_Jasper_Instructions::addInstruction(['type' => 'SetFont', 'font' => $font->fontName, 'fontstyle' => (isset($font->isBold) ? 'B' : ''), 'fontsize' => $font->size]);
                    CReport_Jasper_Instructions::runInstructions();
                    $height_new = $pdf->getStringHeight($width_column, $text) * 1.5;
                    //return default font
                    //$this->SetFont($fontDefault);
                    if ($height_new > $height_header) {
                        $height_header = $height_new;
                    }
                    //echo $height_header;exit;
                }//final max height header ============================

                //height detail default =================================
                if ($column['detailCell']['h'] > $height_detail) {
                    $height_detail = $column['detailCell']['h'];
                }
                //get max height
                if (isset($column['detailCell']['field'])) {
                    $field = $column['detailCell']['field'];
                    //get line spacing
                    $lineHeightRatio = CReport_Jasper_Utils_ElementUtils::getLineHeightRatio($field->xmlElement->textElement, 1.1);

                    $text = $jasperObj->get_expression($field->xmlElement->textFieldExpression, $row);
                    //change font for height row
                    $font = $field->xmlElement->textElement->font->attributes();
                    //$this->SetFont(array("font"=> $font->fontName, "fontstyle"=> (isset($font->isBold)?"B":""), "fontsize"=>$font->size));
                    CReport_Jasper_Instructions::addInstruction(['type' => 'SetFont', 'font' => $font->fontName, 'fontstyle' => (isset($font->isBold) ? 'B' : ''), 'fontsize' => $font->size]);
                    CReport_Jasper_Instructions::runInstructions();
                    $height_new = $pdf->getStringHeight($width_column, $text) * $lineHeightRatio;
                    //return default font
                    //$this->SetFont($fontDefault);
                    if ($height_new > $height_detail) {
                        $height_detail = $height_new;
                    }
                }//end max height header ============================
            }//end get height row header and detail

            //check new page
            CReport_Jasper_Instructions::addInstruction(['type' => 'PreventY_axis', 'y_axis' => $height_detail]);
            CReport_Jasper_Instructions::runInstructions();
            //new page?
            if (self::$page != CReport_Jasper_Instructions::$currrentPage) {
                $showColumnHeader = true;//repeat columnHeader
                $pdf->Ln(0);
                $y = CReport_Jasper_Instructions::$yAxis;
            }

            //posições iniciais
            $startX = $pdf->GetX();
            $startY = CReport_Jasper_Instructions::$yAxis;
            $y = $startY;
            $x = $startX;

            //design tableHeader ===================
            if ($rowIndex == 1) {
                foreach ($arraydata['column'] as $k => $column) {
                    $width_column = $column['w'];
                    $cell = $column['tableHeader'];
                    $borders = $cell['borders'];
                    if (isset($cell['field'])) {
                        $field = $cell['field'];
                        $field->xmlElement->reportElement['x'] = $x - $marginLeft;
                        //$y = $startY+$field->xmlElement->reportElement["y"];
                        $field->xmlElement->reportElement['height'] = $cell['h'];
                        //$field->xmlElement->reportElement["y"]=$y;
                        $field->generate([$jasperObj, $row]);
                        CReport_Jasper_Instructions::runInstructions();
                    }
                    $pdf->SetX($x);
                    //border column
                    if (isset($cell['fillcolor'])) {
                        $pdf->SetFillColor($cell['fillcolor']['r'], $cell['fillcolor']['g'], $cell['fillcolor']['b']);
                    }
                    $pdf->MultiCell($width_column, $cell['h'], '', $borders, 'L', isset($cell['fill']), 0, $x, $y);
                    $x = $x + $width_column;
                    $pdf->SetX($x);
                }//end column

                //start line
                $pdf->Ln(0);
                $x = $startX;
                $y = $y + $cell['h'];
                $pdf->SetX($x);
                self::setYAxis($cell['h']);
            }//end tableHeader

            //design columnHeader table ===================
            if ($showColumnHeader) {
                foreach ($arraydata['column'] as $k => $column) {
                    $width_column = $column['w'];
                    $cell = $column['columnHeader'];
                    $borders = $cell['borders'];
                    if (isset($cell['field'])) {
                        $field = $cell['field'];
                        $field->xmlElement->reportElement['x'] = $x - $marginLeft;
                        //$y = $startY+$field->xmlElement->reportElement["y"];
                        $field->xmlElement->reportElement['height'] = $height_header;
                        //$field->xmlElement->reportElement["y"]=$y;
                        $field->generate([$jasperObj, $row]);
                        CReport_Jasper_Instructions::runInstructions();
                    }
                    $pdf->SetX($x);
                    //border column
                    if (isset($cell['fillcolor'])) {
                        $pdf->SetFillColor($cell['fillcolor']['r'], $cell['fillcolor']['g'], $cell['fillcolor']['b']);
                    }
                    $pdf->MultiCell($width_column, $height_header, '', $borders, 'L', isset($cell['fill']), 0, $x, $y);
                    $x = $x + $width_column;
                    $pdf->SetX($x);
                }//end column each design header

                //start line
                $pdf->Ln(0);
                $x = $startX;
                $y = $y + $height_header;
                $pdf->SetX($x);
                self::setYAxis($height_header);

                $showColumnHeader = false;
            }//final header table

            //designer detail table ===================
            foreach ($arraydata['column'] as $column) {
                $width_column = $column['w'];
                $cell = $column['detailCell'];
                $borders = $cell['borders'];
                if (isset($cell['field'])) {
                    $field = $cell['field'];
                    $field->xmlElement->reportElement['x'] = $x - $marginLeft;
                    $field->xmlElement->reportElement['height'] = $height_detail;
                    //$field->xmlElement->reportElement["y"]=$y;
                    $field->generate([$jasperObj, $row]);
                    CReport_Jasper_Instructions::runInstructions();
                }
                $pdf->SetX($x);
                //border column
                if (isset($cell['fillcolor'])) {
                    $pdf->SetFillColor($cell['fillcolor']['r'], $cell['fillcolor']['g'], $cell['fillcolor']['b']);
                }
                $pdf->MultiCell($width_column, $height_detail, '', $borders, 'L', isset($cell['fill']), 0, $x, $y);
                $x = $x + $width_column;
                $pdf->SetX($x);
            }//end column each design detail

            //start line
            $x = $startX;
            $y = $y + $height_detail;
            $pdf->SetX($x);
            self::setYAxis($height_detail);
            $pdf->Ln(0);
        }//end data each

        //check new page
        if ($height_columnFooter > 0) {
            //check new page
            CReport_Jasper_Instructions::addInstruction(['type' => 'PreventY_axis', 'y_axis' => $height_columnFooter]);
            CReport_Jasper_Instructions::runInstructions();
            //new page?
            if (self::$page != CReport_Jasper_Instructions::$currrentPage) {
                self::$page = CReport_Jasper_Instructions::$currrentPage;
                $pdf->Ln(0);
                $y = CReport_Jasper_Instructions::$yAxis;
            }
        }

        //columnFooter
        foreach ($arraydata['column'] as $column) {
            $width_column = $column['w'];
            if (isset($column['columnFooter'])) {
                $cell = $column['columnFooter'];
                $borders = $cell['borders'];

                //echo $height."<br/>";
                if (isset($cell['field'])) {
                    $field = $cell['field'];
                    $field->xmlElement->reportElement['x'] = $x - $marginLeft;
                    $field->xmlElement->reportElement['height'] = $height_columnFooter;
                    //$field->xmlElement->reportElement["y"]=$y;
                    $field->generate([$jasperObj, null]);
                    CReport_Jasper_Instructions::runInstructions();
                }

                $pdf->SetX($x);
                //border column
                if (isset($cell['fillcolor'])) {
                    $pdf->SetFillColor($cell['fillcolor']['r'], $cell['fillcolor']['g'], $cell['fillcolor']['b']);
                }
                $pdf->MultiCell($width_column, $height_columnFooter, '', $borders, 'L', isset($cell['fill']), 0, $x, $y);
                $x = $x + $width_column;
                $pdf->SetX($x);
            } else {
                break;
            }
        }
        //new line start
        $y = $y + $height_columnFooter;
        $x = $startX;
        $pdf->SetX($x);
        self::setYAxis($height_columnFooter);

        //check new page
        if ($height_tableFooter > 0) {
            //check new page
            CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $height_tableFooter]);
            CReport_Jasper_Instructions::runInstructions();
            //new page?
            if (self::$page != CReport_Jasper_Instructions::$currrentPage) {
                self::$page = CReport_Jasper_Instructions::$currrentPage;
                $pdf->Ln(0);
                $y = CReport_Jasper_Instructions::$yAxis;
            }
        }

        //tableFooter
        foreach ($arraydata['column'] as $column) {
            $width_column = $column['w'];
            if (isset($column['tableFooter'])) {
                $cell = $column['tableFooter'];
                $borders = $cell['borders'];

                //echo $height."<br/>";
                if (isset($cell['field'])) {
                    $field = $cell['field'];
                    $field->xmlElement->reportElement['x'] = $x - $marginLeft;
                    $field->xmlElement->reportElement['height'] = $height_tableFooter;
                    //$field->xmlElement->reportElement["y"]=$y;
                    $field->generate([$jasperObj, null]);
                    CReport_Jasper_Instructions::runInstructions();
                }

                $pdf->SetX($x);
                //border column
                if (isset($cell['fillcolor'])) {
                    $pdf->SetFillColor($cell['fillcolor']['r'], $cell['fillcolor']['g'], $cell['fillcolor']['b']);
                }
                $pdf->MultiCell($width_column, $height_tableFooter, '', $borders, 'L', isset($cell['fill']), 0, $x, $y);
                $x = $x + $width_column;
                $pdf->SetX($x);
            } else {
                break;
            }
        }
        $y = $y + $height_tableFooter;
        $x = $startX;
        $pdf->SetX($x);
        self::setYAxis($height_tableFooter + 10);
    }

    public static function setYAxis($addY_axis) {
        CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $addY_axis]);
        CReport_Jasper_Instructions::runInstructions();
    }
}

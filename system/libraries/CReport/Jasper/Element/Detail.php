<?php

class CReport_Jasper_Element_Detail extends CReport_Jasper_Element {
    public function generate($obj = null) {
        $data = $obj->data;
        /** @var CReport_Jasper_Report $obj */
        if ($this->children) {
            $totalRows = $data->count();

            // $obj->variablesCalculation($obj, $data);
            foreach ($data as $rowIndex => $row) {
                if (CReport_Jasper_Report::$proccessintructionsTime == 'inline') {
                    CReport_Jasper_Instructions::runInstructions();
                }
                // convert array to object
                if (!is_object($row) && is_array($row)) {
                    $row = (object) $row;
                }
                $obj->rowData = $row;

                $row->rowIndex = $rowIndex;

                $obj->arrayVariable['REPORT_COUNT']['ans'] = $rowIndex;
                $obj->arrayVariable['REPORT_COUNT']['target'] = $rowIndex;
                $obj->arrayVariable['REPORT_COUNT']['calculation'] = null;
                $obj->arrayVariable['totalRows']['ans'] = $totalRows;
                $obj->arrayVariable['totalRows']['target'] = $totalRows;
                $obj->arrayVariable['totalRows']['calculation'] = null;
                $row->totalRows = $totalRows;
                if (count($obj->arrayGroup) > 0) {
                    foreach ($obj->arrayGroup as $group) {
                        preg_match_all("/F{(\w+)}/", $group->groupExpression, $matchesF);
                        $groupExpression = $matchesF[1][0];
                        $shouldRender = false;
                        if ($obj->lastRowData) {
                            $lastGroupValue = null;
                            $groupValue = null;

                            if (is_object($obj->lastRowData)) {
                                if ($obj->lastRowData instanceof CCollection) {
                                    $lastGroupValue = $obj->lastRowData->get($groupExpression);
                                    $groupValue = $obj->rowData->get($groupExpression);
                                } else {
                                    $lastGroupValue = $obj->lastRowData->$groupExpression;
                                    $groupValue = $obj->rowData->$groupExpression;
                                }

                                if ($lastGroupValue != $groupValue) {
                                    $shouldRender = true;
                                }
                            } elseif (is_array($obj->lastRowData)) {
                                $lastGroupValue = carr::get($obj->lastRowData, $groupExpression);
                                $groupValue = carr::get($obj->rowData, $groupExpression);

                                if ($lastGroupValue != $groupValue) {
                                    $shouldRender = true;
                                }
                            }
                        }
                        if (($group->resetVariables == 'true' || $shouldRender) && ($group->groupFooter && $rowIndex > 0)) {
                            $groupFooter = new CReport_Jasper_Element_GroupFooter($group->groupFooter);
                            $groupFooter->generate([$obj, $row]);
                            $group->resetVariables = 'false';
                        }

                        if (($rowIndex == 0 || $group->resetVariables == 'true' || $shouldRender) && ($group->groupHeader)) {
                            $groupHeader = new CReport_Jasper_Element_GroupHeader($group->groupHeader);
                            $groupHeader->generate([$obj, $row]);
                            $group->resetVariables = 'false';
                        }
                    }
                }
                $background = $obj->getChildByClassName('Background');

                if ($background) {
                    $background->generate($obj);
                }

                // armazena no array $results;
                foreach ($this->children as $child) {
                    // se for objeto
                    if (is_object($child)) {
                        $print_expression_result = false;
                        $printWhenExpression = (string) $child->objElement->printWhenExpression;
                        if ($printWhenExpression != '') {
                            $printWhenExpression = $obj->getExpression($printWhenExpression, $row);

                            //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
                            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
                        } else {
                            $print_expression_result = true;
                        }
                        $height = (string) $child->objElement['height'];
                        //getHeightMultiCell
                        if (CReport_Jasper_Instructions::$objOutPut instanceof \TCPDF) {
                            $textFields = $child->getChildsByClassName('TextField');
                            $haveStretchOverflow = false;
                            foreach ($textFields as $textField) {
                                if (isset($textField->isStretchWithOverflow) && $textField->isStretchWithOverflow == 'true') {
                                    $haveStretchOverflow = true;

                                    break;
                                }
                            }
                            if ($haveStretchOverflow) {
                                $maxHeight = 0;
                                foreach ($textFields as $textField) {
                                    if ($textField instanceof CReport_Jasper_Element_TextField) {
                                        $multiCellOptions = $textField->getInstructionDataMultiCell([$obj, $row]);

                                        //cdbg::d($multiCellOptions);
                                        $processor = CReport_Jasper_Instructions::getProcessor();
                                        $cellHeight = $processor->getHeightMultiCell($multiCellOptions);
                                        // $cellHeight = 62;
                                        if ($cellHeight > $maxHeight) {
                                            $maxHeight = $cellHeight;
                                        }
                                    }
                                }
                                foreach ($textFields as $textField) {
                                    $textField->forceHeight($maxHeight);
                                }
                                $height = $maxHeight;
                            }
                        }
                        if ($print_expression_result == true) {
                            if ($child->objElement['splitType'] == 'Stretch' || $child->objElement['splitType'] == 'Prevent') {
                                CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $height]);
                            }
                            if (CReport_Jasper_Report::$proccessintructionsTime == 'inline') {
                                CReport_Jasper_Instructions::runInstructions();
                            }
                            $child->generate([$obj, $row]);
                            if ($child->objElement['splitType'] == 'Stretch' || $child->objElement['splitType'] == 'Prevent') {
                                CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
                            }
                            if (CReport_Jasper_Report::$proccessintructionsTime == 'inline') {
                                CReport_Jasper_Instructions::runInstructions();
                            }
                            if ($obj->arrayPageSetting['columnCount'] > 1) {
                                CReport_Jasper_Instructions::addInstruction(['type' => 'changeColumn']);
                                if (is_int($rowIndex / $obj->arrayPageSetting['columnCount'])) {
                                    CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
                                }
                            }
                        }
                    }
                }

                $arrayVariable = ($obj->arrayVariable) ? $obj->arrayVariable : [];
                $recordObject = array_key_exists('recordObj', $arrayVariable) ? $obj->arrayVariable['recordObj']['initialValue'] : 'stdClass';

                $obj->lastRowData = $row;
                $obj->variablesCalculation($obj, $row);
            }
            if (count($obj->arrayGroup) > 0 && $totalRows > 0) {
                foreach ($obj->arrayGroup as $group) {
                    if (($group->groupFooter)) {
                        $groupFooter = new CReport_Jasper_Element_GroupFooter($group->groupFooter);
                        $groupFooter->generate([$obj, $row]);
                        $group->resetVariables = 'false';
                    }
                }
            }

            //$this->close();
        }
    }
}

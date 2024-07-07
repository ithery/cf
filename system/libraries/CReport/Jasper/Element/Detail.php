<?php

class CReport_Jasper_Element_Detail extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $data = $report->getData();

        if ($this->children) {
            $totalRows = $data->count();
            $lastRow = null;
            // $obj->variablesCalculation($obj, $data);
            foreach ($data as $rowIndex => $row) {
                /** @var CReport_Jasper_Report_DataRow $row */
                $report->setCurrentRow($row);

                // convert array to object
                // if (!is_object($row) && is_array($row)) {
                //     $row = (object) $row;
                // }
                //$obj->rowData = $row;

                // $row->rowIndex = $rowIndex;

                $report->arrayVariable['REPORT_COUNT']['ans'] = $rowIndex;
                $report->arrayVariable['REPORT_COUNT']['target'] = $rowIndex;
                $report->arrayVariable['REPORT_COUNT']['calculation'] = null;
                $report->arrayVariable['totalRows']['ans'] = $totalRows;
                $report->arrayVariable['totalRows']['target'] = $totalRows;
                $report->arrayVariable['totalRows']['calculation'] = null;
                // $row->totalRows = $totalRows;

                foreach ($report->getGroupCollection() as $group) {
                    /** @var CReport_Jasper_Report_Group $group */
                    preg_match_all("/F{(\w+)}/", $group->getGroupExpression(), $matchesF);
                    $groupExpression = $matchesF[1][0];
                    $shouldRender = false;
                    if ($lastRow) {
                        $lastGroupValue = carr::get($lastRow, $groupExpression);
                        $groupValue = carr::get($row, $groupExpression);
                        if ($lastGroupValue != $groupValue) {
                            $shouldRender = true;
                        }
                    }

                    if (($rowIndex == 0 || $shouldRender) && ($group->hasGroupHeader())) {
                        $groupHeader = new CReport_Jasper_Element_GroupHeader($group->getGroupHeader());
                        $groupHeader->generate($report);
                    }
                }

                $background = $report->getRoot()->getChildByClassName('Background');

                if ($background) {
                    $background->generate($report);
                }

                // armazena no array $results;
                foreach ($this->children as $child) {
                    // se for objeto
                    if (is_object($child)) {
                        $print_expression_result = false;
                        $printWhenExpression = (string) $child->xmlElement->printWhenExpression;
                        if ($printWhenExpression != '') {
                            $printWhenExpression = $report->getExpression($printWhenExpression, $row);

                            //echo    'if('.$printWhenExpression.'){$print_expression_result=true;}';
                            eval('if(' . $printWhenExpression . '){$print_expression_result=true;}');
                        } else {
                            $print_expression_result = true;
                        }
                        $height = (string) $child->xmlElement['height'];
                        //getHeightMultiCell
                        $processor = $report->getProcessor();

                        if ($processor instanceof CReport_Jasper_Processor_PdfProcessor) {
                            $textFields = $child->getChildsByClassName('TextField');
                            $haveStretchOverflow = false;
                            foreach ($textFields as $textField) {
                                /** @var CReport_Jasper_Element_TextField $textField */
                                if ($textField->getProperty('isStretchWithOverflow') == 'true') {
                                    $haveStretchOverflow = true;

                                    break;
                                }
                            }
                            if ($haveStretchOverflow) {
                                $maxHeight = 0;
                                foreach ($textFields as $textField) {
                                    if ($textField instanceof CReport_Jasper_Element_TextField) {
                                        $multiCellOptions = $textField->getInstructionDataMultiCell($report);

                                        //cdbg::d($multiCellOptions);
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
                            if ($child->xmlElement['splitType'] == 'Stretch' || $child->xmlElement['splitType'] == 'Prevent') {
                                CReport_Jasper_Instructions::addInstruction(['type' => 'preventYAxis', 'y_axis' => $height]);
                            }

                            $child->generate($report);
                            if ($child->xmlElement['splitType'] == 'Stretch' || $child->xmlElement['splitType'] == 'Prevent') {
                                CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
                            }

                            if ($report->arrayPageSetting['columnCount'] > 1) {
                                CReport_Jasper_Instructions::addInstruction(['type' => 'changeColumn']);
                                if (is_int($rowIndex / $report->arrayPageSetting['columnCount'])) {
                                    CReport_Jasper_Instructions::addInstruction(['type' => 'setYAxis', 'y_axis' => $height]);
                                }
                            }
                        }
                    }
                }

                // $arrayVariable = ($report->arrayVariable) ? $report->arrayVariable : [];
                // $recordObject = array_key_exists('recordObj', $arrayVariable) ? $report->arrayVariable['recordObj']['initialValue'] : 'stdClass';
                $report->variablesCalculation($row);
                $nextRow = carr::get($data, $rowIndex + 1);
                foreach ($report->getGroupCollection() as $group) {
                    /** @var CReport_Jasper_Report_Group $group */
                    preg_match_all("/F{(\w+)}/", $group->getGroupExpression(), $matchesF);
                    $groupExpression = $matchesF[1][0];
                    $shouldRender = false;
                    if ($nextRow) {
                        $nextGroupValue = carr::get($nextRow, $groupExpression);
                        $groupValue = carr::get($row, $groupExpression);
                        if ($nextGroupValue != $groupValue) {
                            $shouldRender = true;
                        }
                    } else {
                        $shouldRender = true;
                    }
                    if ($shouldRender && ($group->hasGroupFooter() && $rowIndex > 0)) {
                        // cdbg::dd($group);
                        $groupFooter = new CReport_Jasper_Element_GroupFooter($group->getGroupFooter());
                        $groupFooter->generate($report);
                    }

                    if ($shouldRender) {
                        $group->setResetVariable();
                    }
                }

                $lastRow = $row;
            }

            //$this->close();
        }
    }
}

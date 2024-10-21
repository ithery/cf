<?php

class CReport_Jasper_Element_Line extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $row = $report->getCurrentRow();

        $data = $this->xmlElement;
        $drawcolor = ['r' => 0, 'g' => 0, 'b' => 0];
        $hidden_type = 'line';
        $linewidth = '';
        $dash = '';
        if ($data->graphicElement->pen) {
            if ($data->graphicElement->pen['lineWidth'] > 0) {
                $linewidth = $data->graphicElement->pen['lineWidth'];
            }
        }

        /*
          $borderset="";
          if($data->box->topPen["lineWidth"]>0)
          $borderset.="T";
          if($data->box->leftPen["lineWidth"]>0)
          $borderset.="L";
          if($data->box->bottomPen["lineWidth"]>0)
          $borderset.="B";
          if($data->box->rightPen["lineWidth"]>0)
          $borderset.="R";
          if(isset($data->box->pen["lineColor"])) {
          $drawcolor=array("r"=>hexdec(substr($data->box->pen["lineColor"],1,2)),"g"=>hexdec(substr($data->box->pen["lineColor"],3,2)),"b"=>hexdec(substr($data->box->pen["lineColor"],5,2)));
          }
         */
        if (isset($data->graphicElement->pen['lineStyle'])) {
            if ($data->graphicElement->pen['lineStyle'] == 'Dotted') {
                $dash = '0,1';
            } elseif ($data->graphicElement->pen['lineStyle'] == 'Dashed') {
                $dash = '4,2';
            }
        }

        if (isset($data->reportElement['forecolor'])) {
            $drawcolor = ['r' => hexdec(substr($data->reportElement['forecolor'], 1, 2)), 'g' => hexdec(substr($data->reportElement['forecolor'], 3, 2)), 'b' => hexdec(substr($data->reportElement['forecolor'], 5, 2))];
        }
        //        $this->pointer[]=array("type"=>"SetDrawColor","r"=>$drawcolor["r"],"g"=>$drawcolor["g"],"b"=>$drawcolor["b"],"hidden_type"=>"drawcolor");
        if (isset($data->reportElement['positionType']) && $data->reportElement['positionType'] == 'FixRelativeToBottom') {
            $hidden_type = 'relativebottomline';
        }

        $style = ['color' => $drawcolor, 'width' => (int) $linewidth, 'dash' => $dash];
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
            if ($data->reportElement['width'][0] + 0 > $data->reportElement['height'][0] + 0) {    //width > height means horizontal line
                $report->getInstructions()->addInstruction(CReport_Jasper_Instruction::TYPE_LINE, [
                    'x1' => $data->reportElement['x'] + 0,
                    'y1' => $data->reportElement['y'] + 0,
                    'x2' => $data->reportElement['x'] + $data->reportElement['width'],
                    'y2' => $data->reportElement['y'] + $data->reportElement['height'] - 1,
                    'hidden_type' => $hidden_type,
                    'style' => $style,
                    'forecolor' => $data->reportElement['forecolor'] . '',
                    'printWhenExpression' => $printWhenExpression
                ]);
            } elseif ($data->reportElement['height'][0] + 0 > $data->reportElement['width'][0] + 0) {        //vertical line
                CReport_Jasper_Instructions::addInstruction(['type' => 'line', 'x1' => $data->reportElement['x'], 'y1' => $data->reportElement['y'],
                    'x2' => $data->reportElement['x'] + $data->reportElement['width'] - 1, 'y2' => $data->reportElement['y'] + $data->reportElement['height'], 'hidden_type' => $hidden_type, 'style' => $style,
                    'forecolor' => $data->reportElement['forecolor'] . '', 'printWhenExpression' => $data->reportElement->printWhenExpression]);
            }
            CReport_Jasper_Instructions::addInstruction(['type' => 'setDrawColor', 'r' => 0, 'g' => 0, 'b' => 0, 'hidden_type' => 'drawcolor']);
            CReport_Jasper_Instructions::addInstruction(['type' => 'setFillColor', 'r' => 255, 'g' => 255, 'b' => 255, 'hidden_type' => 'fillcolor']);
        }
        parent::generate($report);
    }
}

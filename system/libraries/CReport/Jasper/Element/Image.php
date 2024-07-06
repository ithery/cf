<?php

class CReport_Jasper_Element_Image extends CReport_Jasper_Element {
    public function generate(CReport_Jasper_Report $report) {
        $row = $report->getCurrentRow();
        $data = $this->xmlElement;
        $text = $data->imageExpression;
        //echo $imagepath;
        //echo $imagepath;
        //$text= substr($data->imageExpression, 1, -1);
        $text = $report->getExpression($text, $row);
        $text = str_ireplace(['"+', '" +', '+"', '+ "', '"'], ['', '', ''], $text);

        $imagetype = substr($text, -3);
        $hyperlinkReferenceExpression = $data->hyperlinkReferenceExpression;
        if ($hyperlinkReferenceExpression) {
            $hyperlinkReferenceExpression = trim(str_replace(['"', ''], '', $data->hyperlinkReferenceExpression));
        }
        // echo $text;

        $arraydata = [
            'type' => 'image',
            'path' => $text,
            'x' => $data->reportElement['x'] + 0,
            'y' => $data->reportElement['y'] + 0,
            'width' => $data->reportElement['width'] + 0,
            'height' => $data->reportElement['height'] + 0,
            'imgtype' => $imagetype,
            'link' => $hyperlinkReferenceExpression,
            'hidden_type' => 'image',
            'linktarget' => $data['hyperlinkTarget'] . '',
            'border' => 0,
            'fitbox' => false
        ];
        if (isset($data->box)) {
            $arraydata['border'] = CReport_Jasper_Utils_ElementUtils::formatBorder($data->box);
        }
        switch ($data['scaleImage']) {
            case 'FillFrame':
                break;
            default:
                switch ($data['hAlign']) {
                    case 'Center':
                        $arraydata['fitbox'] = 'C';

                        break;
                    case 'Right':
                        $arraydata['fitbox'] = 'R';

                        break;
                    default: // "Left"
                        $arraydata['fitbox'] = 'L';

                        break;
                }
                switch ($data['vAlign']) {
                    case 'Middle':
                        $arraydata['fitbox'] .= 'M';

                        break;
                    case 'Bottom':
                        $arraydata['fitbox'] .= 'B';

                        break;
                    default: // "Top"
                        $arraydata['fitbox'] .= 'T';

                        break;
                }
        }
        CReport_Jasper_Instructions::addInstruction($arraydata);
    }
}

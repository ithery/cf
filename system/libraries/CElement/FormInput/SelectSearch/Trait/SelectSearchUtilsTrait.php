<?php

trait CElement_FormInput_SelectSearch_Trait_SelectSearchUtilsTrait {
    protected function generateSelect2TemplateWithData($template, $data) {
        //escape the character
        $template = str_replace("'", "\'", $template);
        preg_match_all("/{([\w]*)}/", $template, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            $str = carr::get($val, 1); //matches str without bracket {}
            $bracketStr = carr::get($val, 0); //matches str with bracket {}
            if (strlen($str) > 0) {
                $dataVal = c::get($data, $str);
                $template = str_replace($bracketStr, $dataVal, $template);
                // $template = str_replace($bracketStr, "'+item." . $str . "+'", $dataVal);
                // $template = str_replace($bracketStr, "'+item." . $str . "+'", $template);
            }
        }

        return $template;
    }

    public function addCAppFormatToData($format, $data, $row, $type = 'result') {
        $type = ucfirst($type);
        if ($format instanceof CFunction_SerializableClosure) {
            $format = $format->__invoke($row);
            if ($format instanceof CRenderable) {
                $data['cappFormat' . $type] = $format->html();
                $data['cappFormat' . $type . 'IsHtml'] = true;
            } else {
                $data['cappFormat' . $type] = $format;
                $data['cappFormat' . $type . 'IsHtml'] = c::isHtml($format);
            }
        } else {
            $template = $this->generateSelect2TemplateWithData($format, $row);
            $data['cappFormat' . $type] = $template;
            $data['cappFormat' . $type . 'IsHtml'] = c::isHtml($template);
        }

        return $data;
    }

    public function modelToSelect2Array(CModel $model) {
        $itemArray = $model->toArray();
        $itemArray['id'] = $model->getKey();

        return $itemArray;
    }
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 15, 2018, 12:46:23 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DialogSelect extends CAjax_Engine {

    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();

        $keyword = carr::get($input, 'keyword');
        $page = carr::get($input, 'page', 1);

        $format = carr::get($data, 'format');
        $keyField = carr::get($data, 'keyField');
        $fields = carr::get($data, 'fields', ['*']);
        if ($fields != ['*']) {
            $fields[] = $keyField;
        }
        $searchField = carr::get($data, 'searchField', []);
        $limit = carr::get($data, 'limit');
        $model = carr::get($data, 'model');
        $itemTemplateName = carr::get($data, 'itemTemplateName');
        $itemTemplateVariables = carr::get($data, 'itemTemplateVariables', []);
        $options = carr::get($data, 'options', []);
        $items = [];
        $total = 0;

        if ($model) {
            foreach ($options as $key => $option) {
                $model = $model->where($key, $option);
            }
            if ($keyword) {
                foreach ($searchField as $key => $field) {
                    if (!$key) {
                        $model = $model->where($field, 'LIKE', "%$keyword%");
                    } else {
                        $model = $model->orWhere($field, 'LIKE', "%$keyword%");
                    }
                }
            }

            // WITH PAGINATE FUNCTION
            $model = $model->paginate($limit, $fields, 'page', $page);
            $total = count($model->items());

            foreach ($model->items() as $item) {
                $arr = array();
                $arr['id'] = '';
                if ($keyField) {
                    $arr['id'] = $item->{$keyField};
                }
                if ($format) {
                    $arr['name'] = $format;
                    preg_match_all("/{(\w*)}/", $format, $matches);
                    foreach ($matches[1] as $key => $match) {
                        $arr['name'] = str_replace("{" . $match . "}", $item->{$match}, $arr['name']);
                    }
                } else {
                    $arr['name'] = $item->{$keyField};
                }
                $items[] = $arr;
            }

            // ALTERNATIF
            // $model = $model->skip(($page - 1) * $limit)->take($limit);
            // $total = $model->count();

            // foreach ($model->get($fields) as $item) {
            //     $arr = array();
            //     $arr['id'] = '';
            //     if ($keyField) {
            //         $arr['id'] = $item->{$keyField};
            //     }
            //     if ($format) {
            //         $arr['name'] = $format;
            //         preg_match_all("/{(.*)}/", $format, $matches);
            //         foreach ($matches as $key => $match) {
            //             $arr['name'] = str_replace("{$match}", $item->{$match}, $arr['name']);
            //         }
            //     } else {
            //         $arr['name'] = $item->{$keyField};
            //     }
            //     $items[] = $arr;
            // }
        }

        $data['result'] = '';

        foreach ($items as $key => $item) {
            $dataVariables = array();

            foreach ($itemTemplateVariables as $varKey => $variable) {
                $dataVariables[$variable] = carr::get($item, $varKey);
            }

            $template = CTemplate::factory($itemTemplateName, $dataVariables);
            $data['result'] .= $template->render();
        }

        $result = array();
        $result["data"] = $data;
        $result["total"] = $total;

        return json_encode($result);
    }

}

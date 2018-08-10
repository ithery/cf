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

        $orgId = carr::get($data, 'orgId');
        $keyField = carr::get($data, 'keyField');
        $fields = carr::get($data, 'fields', ['*']);
        if ($fields != ['*']) {
            $fields[] = $keyField;
        }
        $searchField = carr::get($data, 'searchField', []);
        $limit = carr::get($data, 'limit');
        $model = carr::get($data, 'model');
        $items = [];
        $total = 0;

        if ($model) {
            if ($orgId) {
                $model = $model->where('org_id', $orgId);
            }
            foreach ($searchField as $key => $field) {
                if (!$key) {
                    $model = $model->where($field, 'LIKE', "%$keyword%");
                } else {
                    $model = $model->orWhere($field, 'LIKE', "$keyword");
                }
            }

            $model = $model->paginate($limit, $fields, 'page', $page);
            $total = $model->total();

            foreach ($model->items() as $item) {
                $arr = array();
                $arr['id'] = '';
                if ($keyField) {
                    $arr['id'] = $item->{$keyField};
                }
                $items[] = array_merge($arr, $item->toArray());
            }
        }

        $result = array();
        $data['items'] = $items;
        $result["data"] = $data;
        $result["total"] = $total;

        return json_encode($result);
    }

}

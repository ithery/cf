<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAjax_Engine_DataTable_Processor_ModelQuery extends CAjax_Engine_DataTable_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;
    use CAjax_Engine_DataTable_Trait_ProcessorModelQueryTrait;

    public function process() {
        $request = $this->input;

        $queryUnserialized = $this->table()->getQuery();
        $query = CModel_QuerySerializer::unserialize($queryUnserialized);

        /** @var CModel_Query $query */
        $this->getFullQuery($query);

        $perPage = null;
        $page = 1;

        if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
            $perPage = $request['iDisplayLength'];
            $start = intval($request['iDisplayStart']);

            $page = floor($start / $perPage) + 1;
        }

        $modelCollection = null;
        if ($perPage === null) {
            $modelCollection = $query->get();
            $totalItem = $modelCollection->count();
        } else {
            $models = $query->paginate($perPage, ['*'], 'page', $page);
            $totalItem = $models->total();
            $modelCollection = $models->items();
        }

        $output = [
            'sEcho' => intval(carr::get($request, 'sEcho')),
            'iTotalRecords' => $totalItem,
            'iTotalDisplayRecords' => $totalItem,
            'page' => $page,
            'aaData' => $this->populateAAData($modelCollection, $this->table(), $request, $js),
        ];

        $data = [
            'datatable' => $output,
            'js' => base64_encode($js),
        ];

        return $data;
    }
}

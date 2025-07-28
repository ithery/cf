<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAjax_Engine_SelectSearch_Processor_DataProvider extends CAjax_Engine_SelectSearch_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;

    public function process() {
        $dataProvider = $this->dataProvider();
        /** @var CElement_Depends_DependsOn[] $dependsOn */
        $dependsOn = $this->dependsOn();
        $searchIds = $this->searchIds();
        $keyField = $this->keyField();
        /** @var CManager_Contract_DataProviderInterface $query */
        $dataProvider->searchOr($this->getSearchDataOr());
        $dataProvider->searchFullTextOr($this->getSearchFullTextDataOr());
        $dataProvider->sort($this->getSortData());

        $page = $this->parameter->page();
        $prependData = [];
        $prependDataCount = count($this->prependData());
        if ($page == 1) {
            $prependData = $this->prependData();
        }
        $paginationResult = $dataProvider->paginate($this->parameter->pageSize(), ['*'], 'page', $page, function ($q) use ($dependsOn, $searchIds, $keyField) {
            foreach ($dependsOn as $key => $dependOn) {
                $resolver = $dependOn->getResolver();

                $value = carr::get($this->input, 'dependsOn_' . $key);

                $this->engine->invokeCallback($resolver, [$q, $value]);
            }
            if ($searchIds) {
                if ($q instanceof CModel_Query) {
                    $q->whereIn($keyField, $searchIds);
                }
            }
        });

        $items = c::collect($prependData)->merge($paginationResult->items());

        $data = $items->map(function ($model) {
            $data = $model;
            if (is_string($data)) {
                $str = $data;
                $data = [
                    'id' => $str
                ];
            }
            if ($model instanceof CModel) {
                $data = $model->toArray();
                if ($this->keyField() && $model->{$this->keyField()}) {
                    $data['id'] = $model->{$this->keyField()};
                }
            } else {
                if ($this->keyField() && !isset($data['id'])) {
                    $data['id'] = carr::get($data, $this->keyField());
                }
            }

            $data = $this->addCAppFormatToData($this->formatResult(), $data, $model, 'result');

            $data = $this->addCAppFormatToData($this->formatSelection(), $data, $model, 'selection');

            return $data;
        });
        $total = $paginationResult->total() + $prependDataCount;

        return c::response()->jsonp($this->callback(), [
            'data' => $data,
            'total' => $total,
        ]);
    }

    protected function getSearchDataOr() {
        $searchData = [];
        $searchTerm = $this->searchTerm();
        if (strlen($searchTerm) > 0) {
            foreach ($this->searchField() as $field) {
                if (strlen($field) > 0) {
                    $searchData[$field] = $this->searchTerm();
                }
            }
        }

        return $searchData;
    }

    protected function getSearchFullTextDataOr() {
        $searchData = [];
        $searchTerm = $this->searchTerm();
        if (strlen($searchTerm) > 0) {
            foreach ($this->searchFullTextField() as $field) {
                if (strlen($field) > 0) {
                    $searchData[$field] = $this->searchTerm();
                }
            }
        }

        return $searchData;
    }

    protected function getSortData() {
        return [];
    }
}

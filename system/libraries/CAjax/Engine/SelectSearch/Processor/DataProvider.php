<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 8, 2018, 2:58:18 AM
 */
class CAjax_Engine_SelectSearch_Processor_DataProvider extends CAjax_Engine_SelectSearch_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;

    public function process() {
        $dataProvider = $this->dataProvider();
        /** @var CElement_Depends_DependsOn[] $dependsOn */
        $dependsOn = $this->dependsOn();

        /** @var CManager_Contract_DataProviderInterface $query */
        $dataProvider->searchOr($this->getSearchDataOr());
        $dataProvider->sort($this->getSortData());

        $paginationResult = $dataProvider->paginate($this->parameter->pageSize(), ['*'], 'page', $this->parameter->page(), function ($q) use ($dependsOn) {
            foreach ($dependsOn as $key => $dependOn) {
                $resolver = $dependOn->getResolver();
                $value = carr::get($this->input, 'dependsOn_' . $key);
                $this->engine->invokeCallback($resolver, [$q, $value]);
            }
        });
        $data = c::collect($paginationResult->items())->map(function ($model) {
            return $model->toArray() + [
                'id' => $model->{$this->keyField()}
            ];
        });
        $total = $paginationResult->total();

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

    protected function getSortData() {
        return [];
    }
}

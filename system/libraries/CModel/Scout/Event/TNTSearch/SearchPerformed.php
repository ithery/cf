<?php

class CModel_Scout_Event_TNTSearch_SearchPerformed {
    public function __construct($builder, $results, $isBooleanSearch = false) {
        $this->query = $builder->query;
        $this->isBooleanSearch = (int) $isBooleanSearch;
        $this->indexName = $builder->index ?: $builder->model->searchableAs();
        $this->model = get_class($builder->model);
        $this->ids = $results['ids'];
        $this->hits = $results['hits'];
        $this->execution_time = str_replace(' ms', '', $results['execution_time']);
        $this->driver = CF::config('model.scout.driver');
    }
}

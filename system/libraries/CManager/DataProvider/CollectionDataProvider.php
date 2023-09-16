<?php

use Opis\Closure\SerializableClosure;

class CManager_DataProvider_CollectionDataProvider extends CManager_DataProviderAbstract implements CManager_Contract_DataProviderInterface {
    /**
     * @var CCollection
     */
    protected $data;

    public function __construct($data) {
        $this->data = c::collect($data);
    }

    public function toEnumerable() {
        return $this->data;
    }

    /**
     * @return CCollection
     */
    protected function getFilteredCollection() {
        $collection = $this->data;
        if (count($this->searchOr) > 0) {
            $dataSearch = $this->searchOr;

            $collection = $collection->filter(function ($row) use ($dataSearch) {
                $result = false;
                foreach ($dataSearch as $fieldName => $value) {
                    if ($this->isCallable($value)) {
                        $value = $this->callCallable($value);
                    }
                    $result = $result || (strpos(cstr::lower(carr::get($row, $fieldName)), cstr::lower($value)) !== false);
                }

                return $result;
            });
        }

        if (count($this->searchAnd) > 0) {
            $dataSearch = $this->searchAnd;

            $collection = $collection->filter(function ($row) use ($dataSearch) {
                $result = true;
                foreach ($dataSearch as $fieldName => $value) {
                    if ($this->isCallable($value)) {
                        $value = $this->callCallable($value);
                    }
                    $result = $result && (strpos(cstr::lower(carr::get($row, $fieldName)), cstr::lower($value)) !== false);
                }

                return $result;
            });
        }
        //process ordering
        if (count($this->sort) > 0) {
            foreach ($this->sort as $fieldName => $sortDirection) {
                $collection = $collection->sortBy($fieldName, SORT_REGULAR, cstr::upper($sortDirection) == 'DESC');
            }
        }

        return $collection;
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null) {
        $data = $this->getFilteredCollection();
        $total = $data->count();
        $results = $data->forPage($page, $perPage);
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        return c::paginator($results, $total, $perPage, $page, [
            'path' => CPagination_Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);
    }

    /**
     * @param string $method
     * @param string $column
     *
     * @return mixed
     */
    public function aggregate($method, $column) {
        if (!$this->isValidAggregateMethod($method)) {
            throw new Exception($method . ': is not valid aggregate method');
        }
        if ($method == 'count') {
            return $this->data->count();
        }

        return $this->data->$method($column);
    }
}

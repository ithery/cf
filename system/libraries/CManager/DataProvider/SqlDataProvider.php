<?php

use Opis\Closure\SerializableClosure;

class CManager_DataProvider_SqlDataProvider extends CManager_DataProviderAbstract implements CManager_Contract_DataProviderInterface {
    protected $connection = '';

    protected $sql;

    protected $bindings;

    private $baseQuery;

    private $baseOrder;

    public function __construct($sql, $bindings = []) {
        $this->sql = $sql;
        $this->bindings = $bindings;
    }

    protected function getQuery() {
        return $this->getDb()->compileBinds($this->sql, $this->bindings);
    }

    public function setConnection($connection) {
        $this->connection = $connection instanceof Closure ? new SerializableClosure($connection) : $connection;
    }

    public function getConnection() {
        $connection = c::value($this->connection) ?: 'default';
        if ($connection instanceof SerializableClosure) {
            $connection = $connection->__invoke();
        }

        return $connection;
    }

    public function getDb() {
        $connection = $this->getConnection();

        return $connection instanceof CDatabase_Connection ? $connection : c::db($connection);
    }

    public function toEnumerable() {
        $sql = $this->getFullQuery();

        return c::collect($this->getDb()->query($sql)->resultArray(false));
    }

    private function executeQuery($sql, $bindings = []) {
        return $this->getDb()->query($sql, $bindings);
    }

    public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $callback = null) {
        $page = $page ?: CPagination_Paginator::resolveCurrentPage($pageName);

        $total = $this->getTotalFilteredRecord();
        $results = c::collect();
        if ($total > 0) {
            $query = $this->getQueryForPage($page, $perPage);

            $resultQ = $this->executeQuery($query);
            $results = new CCollection_LazyCollection($resultQ->result(false));
        }

        $paginator = c::paginator($results, $total, $perPage, $page, [
            'path' => CPagination_Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ]);

        return $paginator;
    }

    public function getCountForPagination() {
        return $this->getTotalRecord();
    }

    protected function getBaseQuery() {
        if ($this->baseQuery === null) {
            $qBase = $this->getQuery();

            $maxWhere = strrpos(strtolower($qBase), 'where');
            $maxFrom = strrpos(strtolower($qBase), 'from');

            $maxOffset = 0;
            if ($maxWhere !== false && $maxWhere > $maxOffset) {
                $maxOffset = $maxWhere;
            }
            if ($maxFrom !== false && $maxFrom > $maxOffset) {
                $maxOffset = $maxFrom;
            }

            $posOrderBy = strrpos(strtolower($qBase), 'order by', $maxOffset);

            $postLastBracket = strrpos(strtolower($qBase), ')');

            $stringOrderBy = '';
            if ($posOrderBy !== false && $posOrderBy > $postLastBracket) {
                $stringOrderBy = substr($qBase, $posOrderBy, strlen($qBase) - $posOrderBy);
                $qBase = substr($qBase, 0, $posOrderBy);
            }
            $this->baseQuery = $qBase;
            $this->baseOrder = $stringOrderBy;
        }

        return $this->baseQuery;
    }

    protected function getQueryLimit($page, $perPage) {
        $connection = $this->getDb();
        $driver = $connection->getDriverName();
        if ($page <= 0) {
            return '';
        }
        $offset = ($page - 1) * $perPage;
        $limit = $perPage;
        $sLimit = 'LIMIT ' . intval($offset) . ', ' . intval($limit);
        if ($driver == 'pgsql') {
            $sLimit = 'LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
        }

        return $sLimit;
    }

    protected function getQueryOrderBy() {
        $sortData = $this->sort;

        $sOrder = '';
        //process ordering
        if (count($this->sort) > 0) {
            foreach ($this->sort as $fieldName => $sortDirection) {
                $sOrder .= ', ' . $this->getDb()->escapeColumn($fieldName) . ' ' . $this->getDb()->escapeStr($sortDirection);
            }
        }

        if (strlen($sOrder) > 0) {
            $sOrder = substr($sOrder, 2);
        }

        if (strlen($sOrder) == 0) {
            $stringOrderBy = $this->baseOrder();

            if (strlen($stringOrderBy) > 0) {
                //remove prefixed column from order by
                $sub = explode(',', substr($stringOrderBy, 9));
                $sOrder = '';
                $newStringOrderBy = '';
                foreach ($sub as $val) {
                    $columnNames = explode('.', $val);
                    $columnName = $columnNames[0];
                    if (isset($columnNames[1])) {
                        $columnName = $columnNames[1];
                    }
                    $newStringOrderBy .= ', ' . $columnName;
                }
                $sOrder = substr($newStringOrderBy, 2);
            }
        }
        if (strlen($sOrder) > 0) {
            $sOrder = 'ORDER BY ' . $sOrder;
        }

        return $sOrder;
    }

    protected function baseOrder() {
        if ($this->baseOrder === null) {
            $this->getBaseQuery();
        }

        return $this->baseOrder;
    }

    protected function getQueryWhere() {
        $sWhereOr = '';
        $sWhereAnd = '';
        $sWhere = '';

        $connection = $this->getDb();
        $driver = $connection->getDriverName();
        //process search
        if (count($this->searchOr) > 0) {
            $dataSearchOr = $this->searchOr;

            foreach ($dataSearchOr as $fieldName => $value) {
                $column = $this->getDb()->escapeColumn($fieldName);
                if ($driver === 'pgsql') {
                    $column = '"' . $fieldName . '"' . '::text';
                }
                $sWhereOr .= 'OR ' . $column . " LIKE '%" . $this->getDb()->escapeLike($value) . "%' ";
            }
            if (strlen($sWhereOr) > 0) {
                $sWhereOr = '(' . substr($sWhereOr, 3) . ')';
            }
        }

        if (count($this->searchAnd) > 0) {
            $dataSearchAnd = $this->searchAnd;

            foreach ($dataSearchAnd as $fieldName => $value) {
                $column = $this->getDb()->escapeColumn($fieldName);
                if ($driver === 'pgsql') {
                    $column = '"' . $fieldName . '"' . '::text';
                }
                $sWhereAnd .= 'AND ' . $column . " LIKE '%" . $this->getDb()->escapeLike($value) . "%' ";
            }
            if (strlen($sWhereAnd) > 0) {
                $sWhereAnd = '(' . substr($sWhereAnd, 4) . ')';
            }
        }

        $sWhere = $sWhereOr;
        if (strlen($sWhereAnd) > 0) {
            if (strlen($sWhere) > 0) {
                $sWhere .= ' AND ';
            }
            $sWhere .= $sWhereAnd;
        }
        if (strlen($sWhere) > 0) {
            $sWhere = ' WHERE ( ' . $sWhere . ' )';
        }

        return $sWhere;
    }

    public function getTotalRecord() {
        $q = $this->getBaseQuery();
        // get total record
        $qTotal = 'select count(*) as cnt from (' . $q . ') as a';
        $rTotal = $this->getDb()->query($qTotal);
        $totalRecord = 0;
        if ($rTotal->count() > 0) {
            $totalRecord = $rTotal[0]->cnt;
        }

        return $totalRecord;
    }

    public function getTotalFilteredRecord() {
        $qBase = $this->getBaseQuery();
        $sWhere = $this->getQueryWhere();
        $qFiltered = 'select * from (' . $qBase . ') as a ' . $sWhere;

        $qTotalFiltered = 'select count(*) as cnt from (' . $qFiltered . ') as a';
        $rTotalFiltered = $this->getDb()->query($qTotalFiltered);
        $totalFilteredRecord = 0;
        if ($rTotalFiltered->count() > 0) {
            $totalFilteredRecord = $rTotalFiltered[0]->cnt;
        }

        return $totalFilteredRecord;
    }

    protected function getQueryForPage($page, $perPage) {
        $q = $this->getFullQuery();
        $sLimit = $this->getQueryLimit($page, $perPage);
        $q .= ' ' . $sLimit;

        return $q;
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
        $q = $this->getFullQuery();
        $alias = $method . '_' . $column;
        $qTotal = 'select ' . $method . '(' . $this->getDb()->escapeColumn($column) . ') as ' . $alias . ' from (' . $q . ') as t';
        $rTotal = $this->getDb()->query($qTotal);

        return $rTotal[0]->$alias;
    }

    protected function getFullQuery() {
        $qBase = $this->getBaseQuery();

        /* Ordering */
        $sOrder = $this->getQueryOrderBy();
        /**
         * Build condition query.
         */
        $sWhere = $this->getQueryWhere();

        $qProcess = 'select * from (' . $qBase . ') as a ' . $sWhere . ' ' . $sOrder;

        return $qProcess;
    }
}

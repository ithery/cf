<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CModel_Nested_Builder extends CDatabase_Query_Builder {

    /**
     * Replace the "order by" clause of the current query.
     *
     * @param string $column
     * @param string $direction
     *
     * @return CModel_Nested_Builder|static
     */
    public function reOrderBy($column, $direction = 'asc') {
        $this->orders = null;

        if (!is_null($column)) {
            return $this->orderBy($column, $direction);
        }

        return $this;
    }

    /**
     * Execute an aggregate function on the database.
     *
     * @param string $function
     * @param array  $columns
     *
     * @return mixed
     */
    public function aggregate($function, $columns = ['*']) {
        // Postgres doesn't like ORDER BY when there's no GROUP BY clause
        if (!isset($this->groups)) {
            $this->reOrderBy(null);
        }

        return parent::aggregate($function, $columns);
    }

}

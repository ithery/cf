<?php

trait CDatabase_Trait_ExplainsQueries {
    /**
     * Explains the query.
     *
     * @return CCollection
     */
    public function explain() {
        $sql = $this->toSql();

        $bindings = $this->getBindings();

        $explanation = $this->getConnection()->select('EXPLAIN ' . $sql, $bindings);

        return new CCollection($explanation);
    }
}

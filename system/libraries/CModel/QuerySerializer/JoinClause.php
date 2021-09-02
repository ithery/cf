<?php

class CModel_QuerySerializer_JoinClause {
    /**
     * Serialize to Join Clause Query Builder.
     */
    public static function serialize(CDatabase_Query_JoinClause $builder) {
        return array_merge(CModel_QuerySerializer_Query::serialize($builder), [
            'type' => $builder->type,
            'table' => $builder->table,
        ]);
    }

    /**
     * Unserialize to Join Clause Query Builder.
     */
    public static function unserialize(CDatabase_Query_Builder $builder, array $joins) {
        $results = [];

        foreach ($joins as $join) {
            $type = $join['type'];
            $table = $join['table'];

            $joinClauseBuilder = new CDatabase_Query_JoinClause(
                $builder,
                $type,
                $table
            );

            CModel_QuerySerializer_Query::unserializeFor($joinClauseBuilder, carr::except($join, ['type', 'table']));

            $results[] = $joinClauseBuilder;
        }

        return $results;
    }
}

<?php
class CModel_Relation_HasOne extends CModel_Relation_HasOneOrMany {
    use CModel_Relation_Trait_CanBeOneOfMany;
    use CModel_Relation_Trait_ComparesRelatedModels;
    use CModel_Relation_Trait_SupportsDefaultModels;

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     *
     * @phpstan-return ?TRelatedModel
     */
    public function getResults() {
        if (is_null($this->getParentKey())) {
            return $this->getDefaultFor($this->parent);
        }

        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param array  $models
     * @param string $relation
     *
     * @return array
     */
    public function initRelation(array $models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->getDefaultFor($model));
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param array             $models
     * @param CModel_Collection $results
     * @param string            $relation
     *
     * @return array
     */
    public function match(array $models, CModel_Collection $results, $relation) {
        return $this->matchOne($models, $results, $relation);
    }

    /**
     * Add the constraints for an internal relationship existence query.
     *
     * Essentially, these queries compare on column names like "whereColumn".
     *
     * @param \CModel_Query $query
     * @param \CModel_Query $parentQuery
     * @param array|mixed   $columns
     *
     * @return \CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        if ($this->isOneOfMany()) {
            $this->mergeOneOfManyJoinsTo($query);
        }

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    /**
     * Add constraints for inner join subselect for one of many relationships.
     *
     * @param \CModel_Query $query
     * @param null|string   $column
     * @param null|string   $aggregate
     *
     * @return void
     */
    public function addOneOfManySubQueryConstraints(CModel_Query $query, $column = null, $aggregate = null) {
        $query->addSelect($this->foreignKey);
    }

    /**
     * Get the columns that should be selected by the one of many subquery.
     *
     * @return array|string
     */
    public function getOneOfManySubQuerySelectColumns() {
        return $this->foreignKey;
    }

    /**
     * Add join query constraints for one of many relationships.
     *
     * @param \CDatabase_Query_JoinClause $join
     *
     * @return void
     */
    public function addOneOfManyJoinSubQueryConstraints(CDatabase_Query_JoinClause $join) {
        $join->on($this->qualifySubSelectColumn($this->foreignKey), '=', $this->qualifyRelatedColumn($this->foreignKey));
    }

    /**
     * Make a new related instance for the given model.
     *
     * @param CModel $parent
     *
     * @return CModel
     */
    public function newRelatedInstanceFor(CModel $parent) {
        return $this->related->newInstance()->setAttribute(
            $this->getForeignKeyName(),
            $parent->{$this->localKey}
        );
    }

    /**
     * Get the value of the model's foreign key.
     *
     * @param CModel $model
     *
     * @return mixed
     */
    protected function getRelatedKeyFrom(CModel $model) {
        return $model->getAttribute($this->getForeignKeyName());
    }
}

<?php

class CModel_Relation_HasOne extends CModel_Relation_HasOneOrMany {
    use CModel_Relation_Trait_SupportsDefaultModels;

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
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
}

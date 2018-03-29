<?php

class CModel_Relation_HasMany extends CModel_Relation_HasOneOrMany {


    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        return $this->query->get();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array   $models
     * @param  string  $relation
     * @return array
     */
    public function initRelation(array $models, $relation) {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array   $models
     * @param  CModel_Collection  $results
     * @param  string  $relation
     * @return array
     */
    public function match(array $models, CModel_Collection $results, $relation) {
        return $this->matchMany($models, $results, $relation);
    }

}

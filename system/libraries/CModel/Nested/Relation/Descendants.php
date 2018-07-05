<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 2:22:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Nested_Relation_Descendants extends CModel_Nested_Relation {

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        if (!static::$constraints)
            return;
        $this->query->whereDescendantOf($this->parent);
    }

    /**
     * @param QueryBuilder $query
     * @param Model $model
     */
    protected function addEagerConstraint($query, $model) {
        $query->orWhereDescendantOf($model);
    }

    /**
     * @param Model $model
     * @param $related
     *
     * @return mixed
     */
    protected function matches(CModel $model, $related) {
        return $related->isDescendantOf($model);
    }

    /**
     * @param $hash
     * @param $table
     * @param $lft
     * @param $rgt
     *
     * @return string
     */
    protected function relationExistenceCondition($hash, $table, $lft, $rgt) {
        return "{$hash}.{$lft} between {$table}.{$lft} + 1 and {$table}.{$rgt}";
    }

}

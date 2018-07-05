<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 2:21:48 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CModel_Nested_Relation_Ancestors extends CModel_Nested_Relation {

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        if (!static::$constraints)
            return;
        $this->query->whereAncestorOf($this->parent)->defaultOrder();
    }

    /**
     * @param Model $model
     * @param $related
     *
     * @return bool
     */
    protected function matches(CModel $model, $related) {
        return $related->isAncestorOf($model);
    }

    /**
     * @param QueryBuilder $query
     * @param Model $model
     *
     * @return void
     */
    protected function addEagerConstraint($query, $model) {
        $query->orWhereAncestorOf($model);
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
        $key = $this->getBaseQuery()->getGrammar()->wrap($this->parent->getKeyName());
        return "{$table}.{$rgt} between {$hash}.{$lft} and {$hash}.{$rgt} and $table.$key <> $hash.$key";
    }

}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 2:20:56 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CModel_Nested_Relation extends CModel_Relation {

    /**
     * @var CModel_Nested_Query
     */
    protected $query;

    /**
     * @var CModel_Nested_Trait|Model
     */
    protected $parent;

    /**
     * The count of self joins.
     *
     * @var int
     */
    protected static $selfJoinCount = 0;

    /**
     * AncestorsRelation constructor.
     *
     * @param CModel_Nested_Query $builder
     * @param Model $model
     */
    public function __construct(CModel_Nested_Query $builder, CModel $model) {
        if (!CModel_Nested_NestedSet::isNode($model)) {
            throw new InvalidArgumentException('Model must be node.');
        }
        parent::__construct($builder, $model);
    }

    /**
     * @param Model $model
     * @param $related
     *
     * @return bool
     */
    abstract protected function matches(CModel $model, $related);

    /**
     * @param CModel_Nested_Query $query
     * @param Model $model
     *
     * @return void
     */
    abstract protected function addEagerConstraint($query, $model);

    /**
     * @param $hash
     * @param $table
     * @param $lft
     * @param $rgt
     *
     * @return string
     */
    abstract protected function relationExistenceCondition($hash, $table, $lft, $rgt);

    /**
     * @param CModel_Query $query
     * @param CModel_Query $parent
     * @param array $columns
     *
     * @return mixed
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parent, $columns = ['*']
    ) {
        $query = $this->getParent()->replicate()->newScopedQuery()->select($columns);
        $table = $query->getModel()->getTable();
        $query->from($table . ' as ' . $hash = $this->getRelationCountHash());
        $query->getModel()->setTable($hash);
        $grammar = $query->getQuery()->getGrammar();
        $condition = $this->relationExistenceCondition(
                $grammar->wrapTable($hash), $grammar->wrapTable($table), $grammar->wrap($this->parent->getLftName()), $grammar->wrap($this->parent->getRgtName()));
        return $query->whereRaw($condition);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array $models
     * @param  string $relation
     *
     * @return array
     */
    public function initRelation(array $models, $relation) {
        return $models;
    }

    /**
     * @param CModel_Query $query
     * @param CModel_Query $parent
     * @param array $columns
     *
     * @return mixed
     */
    public function getRelationQuery(
    CModel_Query $query, CModel_Query $parent, $columns = ['*']
    ) {
        return $this->getRelationExistenceQuery($query, $parent, $columns);
    }

    /**
     * Get a relationship join table hash.
     *
     * @return string
     */
    public function getRelationCountHash() {
        return 'nested_set_' . self::$selfJoinCount++;
    }

    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults() {
        return $this->query->get();
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param  array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $this->query->whereNested(function (Builder $inner) use ($models) {
            // We will use this query in order to apply constraints to the
            // base query builder
            $outer = $this->parent->newQuery()->setQuery($inner);
            foreach ($models as $model) {
                $this->addEagerConstraint($outer, $model);
            }
        });
    }

    /**
     * Match the eagerly loaded results to their parents.
     *
     * @param  array $models
     * @param  CModel_Collection $results
     * @param  string $relation
     *
     * @return array
     */
    public function match(array $models, CModel_Collection $results, $relation) {
        foreach ($models as $model) {
            $related = $this->matchForModel($model, $results);
            $model->setRelation($relation, $related);
        }
        return $models;
    }

    /**
     * @param Model $model
     * @param CModel_Collection $results
     *
     * @return Collection
     */
    protected function matchForModel(Model $model, CModel_Collection $results) {
        $result = $this->related->newCollection();
        foreach ($results as $related) {
            if ($this->matches($model, $related)) {
                $result->push($related);
            }
        }
        return $result;
    }

}

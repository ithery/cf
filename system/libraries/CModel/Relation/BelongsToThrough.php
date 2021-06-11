<?php

class CModel_Relation_BelongsToThrough extends CModel_Relation {
    use CModel_Relation_Trait_SupportsDefaultModels;

    /**
     * The column alias for the local key on the first "through" parent model.
     *
     * @var string
     */
    const THROUGH_KEY = 'cmodel_through_key';

    /**
     * The "through" parent model instances.
     *
     * @var CModel[]
     */
    protected $throughParents;

    /**
     * The foreign key prefix for the first "through" parent model.
     *
     * @var string
     */
    protected $prefix;

    /**
     * The custom foreign keys on the relationship.
     *
     * @var array
     */
    protected $foreignKeyLookup;

    /**
     * Create a new belongs to through relationship instance.
     *
     * @param \CModel_Query $query
     * @param CModel        $parent
     * @param CModel[]      $throughParents
     * @param string|null   $localKey
     * @param string        $prefix
     * @param array         $foreignKeyLookup
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $parent, array $throughParents, $localKey = null, $prefix = '', array $foreignKeyLookup = []) {
        $this->throughParents = $throughParents;
        $this->prefix = $prefix;
        $this->foreignKeyLookup = $foreignKeyLookup;

        parent::__construct($query, $parent);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        $this->performJoins();

        if (static::$constraints) {
            $localValue = $this->parent[$this->getFirstForeignKeyName()];

            $this->query->where($this->getQualifiedFirstLocalKeyName(), '=', $localValue);
        }
    }

    /**
     * Set the join clauses on the query.
     *
     * @param \CModel_Query|null $query
     *
     * @return void
     */
    protected function performJoins(CModel_Query $query = null) {
        $query = $query ?: $this->query;

        foreach ($this->throughParents as $i => $model) {
            $predecessor = $i > 0 ? $this->throughParents[$i - 1] : $this->related;

            $first = $model->qualifyColumn($this->getForeignKeyName($predecessor));

            $second = $predecessor->getQualifiedKeyName();

            $query->join($model->getTable(), $first, '=', $second);

            if ($this->hasSoftDeletes($model)) {
                $this->query->whereNull($model->getQualifiedDeletedAtColumn());
            }
        }
    }

    /**
     * Get the foreign key for a model.
     *
     * @param CModel|null $model
     *
     * @return string
     */
    public function getForeignKeyName(CModel $model = null) {
        $model = ($model ? $model : $this->parent);
        $table = explode(' as ', $model->getTable())[0];

        if (array_key_exists($table, $this->foreignKeyLookup)) {
            return $this->foreignKeyLookup[$table];
        }

        return cstr::singular($table) . '_id';
    }

    /**
     * Determine whether a model uses SoftDeletes.
     *
     * @param CModel $model
     *
     * @return bool
     */
    public function hasSoftDeletes(CModel $model) {
        return in_array(SoftDeletes::class, c::classUsesRecursive($model));
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $keys = $this->getKeys($models, $this->getFirstForeignKeyName());

        $this->query->whereIn($this->getQualifiedFirstLocalKeyName(), $keys);
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param CModel[] $models
     * @param string   $relation
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
     * @param CModel[]           $models
     * @param \CModel_Collection $results
     * @param string             $relation
     *
     * @return array
     */
    public function match(array $models, CModel_Collection $results, $relation) {
        $dictionary = $this->buildDictionary($results);

        foreach ($models as $model) {
            $key = $model[$this->getFirstForeignKeyName()];

            if (isset($dictionary[$key])) {
                $model->setRelation($relation, $dictionary[$key]);
            }
        }

        return $models;
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param \CModel_Collection $results
     *
     * @return array
     */
    protected function buildDictionary(CModel_Collection $results) {
        $dictionary = [];

        foreach ($results as $result) {
            $dictionary[$result[static::THROUGH_KEY]] = $result;

            unset($result[static::THROUGH_KEY]);
        }

        return $dictionary;
    }

    /**
     * Get the results of the relationship.
     *
     * @return CModel
     */
    public function getResults() {
        return $this->first() ?: $this->getDefaultFor($this->parent);
    }

    /**
     * Execute the query and get the first result.
     *
     * @param array $columns
     *
     * @return CModel|object|static|null
     */
    public function first($columns = ['*']) {
        if ($columns === ['*']) {
            $columns = [$this->related->getTable() . '.*'];
        }

        return $this->query->first($columns);
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @param array $columns
     *
     * @return \CModel_Collection
     */
    public function get($columns = ['*']) {
        $columns = $this->query->getQuery()->columns ? [] : $columns;

        if ($columns === ['*']) {
            $columns = [$this->related->getTable() . '.*'];
        }

        $columns[] = $this->getQualifiedFirstLocalKeyName() . ' as ' . static::THROUGH_KEY;

        $this->query->addSelect($columns);

        return $this->query->get();
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @param \CModel_Query $query
     * @param \CModel_Query $parent
     * @param array|mixed   $columns
     *
     * @return \CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parent, $columns = ['*']) {
        $this->performJoins($query);

        $foreignKey = $parent->getQuery()->from . '.' . $this->getFirstForeignKeyName();

        return $query->select($columns)->whereColumn(
            $this->getQualifiedFirstLocalKeyName(),
            '=',
            $foreignKey
        );
    }

    /**
     * Restore soft-deleted models.
     *
     * @param array|string ...$columns
     *
     * @return $this
     */
    public function withTrashed(...$columns) {
        if (empty($columns)) {
            $this->query->withTrashed();

            return $this;
        }

        if (is_array($columns[0])) {
            $columns = $columns[0];
        }

        $this->query->getQuery()->wheres = c::collect($this->query->getQuery()->wheres)
            ->reject(function ($where) use ($columns) {
                return $where['type'] === 'Null' && in_array($where['column'], $columns);
            })->values()->all();

        return $this;
    }

    /**
     * Get the foreign key for the first "through" parent model.
     *
     * @return string
     */
    public function getFirstForeignKeyName() {
        return $this->prefix . $this->getForeignKeyName(end($this->throughParents));
    }

    /**
     * Get the qualified local key for the first "through" parent model.
     *
     * @return string
     */
    public function getQualifiedFirstLocalKeyName() {
        return end($this->throughParents)->getQualifiedKeyName();
    }

    /**
     * Make a new related instance for the given model.
     *
     * @param CModel $parent
     *
     * @return CModel
     */
    protected function newRelatedInstanceFor(CModel $parent) {
        return $this->related->newInstance();
    }
}

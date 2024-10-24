<?php
/**
 * @template TRelatedModel of \CModel
 *
 * @extends CModel_Relation<TRelatedModel>
 */
abstract class CModel_Relation_HasOneOrMany extends CModel_Relation {
    use CModel_Relation_Trait_InteractsWithDictionary;

    /**
     * The foreign key of the parent model.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The local key of the parent model.
     *
     * @var string
     */
    protected $localKey;

    /**
     * Create a new has one or many relationship instance.
     *
     * @param CModel_Query $query
     * @param CModel       $parent
     * @param string       $foreignKey
     * @param string       $localKey
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $parent, $foreignKey, $localKey) {
        $this->localKey = $localKey;
        $this->foreignKey = $foreignKey;

        parent::__construct($query, $parent);
    }

    /**
     * Create and return an un-saved instance of the related model.
     *
     * @param array $attributes
     *
     * @return CModel
     */
    public function make(array $attributes = []) {
        return c::tap($this->related->newInstance($attributes), function ($instance) {
            $this->setForeignAttributesForCreate($instance);
        });
    }

    /**
     * Create and return an un-saved instances of the related models.
     *
     * @param iterable $records
     *
     * @return CModel_Collection
     */
    public function makeMany($records) {
        $instances = $this->related->newCollection();

        foreach ($records as $record) {
            $instances->push($this->make($record));
        }

        return $instances;
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        if (static::$constraints) {
            $query = $this->getRelationQuery();

            $query->where($this->foreignKey, '=', $this->getParentKey());

            $query->whereNotNull($this->foreignKey);
        }
    }

    /**
     * Set the constraints for an eager load of the relation.
     *
     * @param array $models
     *
     * @return void
     */
    public function addEagerConstraints(array $models) {
        $whereIn = $this->whereInMethod($this->parent, $this->localKey);

        $this->whereInEager(
            $whereIn,
            $this->foreignKey,
            $this->getKeys($models, $this->localKey),
            $this->getRelationQuery()
        );
    }

    /**
     * Match the eagerly loaded results to their single parents.
     *
     * @param array             $models
     * @param CModel_Collection $results
     * @param string            $relation
     *
     * @return array
     */
    public function matchOne(array $models, CModel_Collection $results, $relation) {
        return $this->matchOneOrMany($models, $results, $relation, 'one');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param array             $models
     * @param CModel_Collection $results
     * @param string            $relation
     *
     * @return array
     */
    public function matchMany(array $models, CModel_Collection $results, $relation) {
        return $this->matchOneOrMany($models, $results, $relation, 'many');
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param array             $models
     * @param CModel_Collection $results
     * @param string            $relation
     * @param string            $type
     *
     * @return array
     */
    protected function matchOneOrMany(array $models, CModel_Collection $results, $relation, $type) {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $this->getDictionaryKey($model->getAttribute($this->localKey))])) {
                $model->setRelation(
                    $relation,
                    $this->getRelationValue($dictionary, $key, $type)
                );
            }
        }

        return $models;
    }

    /**
     * Get the value of a relationship by one or many type.
     *
     * @param array  $dictionary
     * @param string $key
     * @param string $type
     *
     * @return mixed
     */
    protected function getRelationValue(array $dictionary, $key, $type) {
        $value = $dictionary[$key];

        return $type == 'one' ? reset($value) : $this->related->newCollection($value);
    }

    /**
     * Build model dictionary keyed by the relation's foreign key.
     *
     * @param CModel_Collection $results
     *
     * @return array
     */
    protected function buildDictionary(CModel_Collection $results) {
        $foreign = $this->getForeignKeyName();

        return $results->mapToDictionary(function ($result) use ($foreign) {
            return [$this->getDictionaryKey($result->{$foreign}) => $result];
        })->all();
    }

    /**
     * Find a model by its primary key or return new instance of the related model.
     *
     * @param mixed $id
     * @param array $columns
     *
     * @return CCollection|CModel
     */
    public function findOrNew($id, $columns = ['*']) {
        if (is_null($instance = $this->find($id, $columns))) {
            $instance = $this->related->newInstance();

            $this->setForeignAttributesForCreate($instance);
        }

        return $instance;
    }

    /**
     * Get the first related model record matching the attributes or instantiate it.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return CModel
     *
     * @phpsta-return TModelClass|CModel
     */
    public function firstOrNew(array $attributes, array $values = []) {
        if (is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->related->newInstance($attributes + $values);

            $this->setForeignAttributesForCreate($instance);
        }

        return $instance;
    }

    /**
     * Get the first related record matching the attributes or create it.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return CModel
     */
    public function firstOrCreate(array $attributes, array $values = []) {
        if (is_null($instance = $this->where($attributes)->first())) {
            $instance = $this->create($attributes + $values);
        }

        return $instance;
    }

    /**
     * Create or update a related record matching the attributes, and fill it with values.
     *
     * @param array $attributes
     * @param array $values
     *
     * @return CModel
     */
    public function updateOrCreate(array $attributes, array $values = []) {
        return c::tap($this->firstOrNew($attributes), function ($instance) use ($values) {
            $instance->fill($values);

            $instance->save();
        });
    }

    /**
     * Attach a model instance to the parent model.
     *
     * @param CModel $model
     *
     * @return CModel|false
     */
    public function save(CModel $model) {
        $this->setForeignAttributesForCreate($model);

        return $model->save() ? $model : false;
    }

    /**
     * Attach a model instance without raising any events to the parent model.
     *
     * @param \CModel $model
     *
     * @return \CModel|false
     */
    public function saveQuietly(CModel $model) {
        return CModel::withoutEvents(function () use ($model) {
            return $this->save($model);
        });
    }

    /**
     * Attach a collection of models to the parent instance.
     *
     * @param iterable|array $models
     *
     * @return iterable|array
     */
    public function saveMany($models) {
        foreach ($models as $model) {
            $this->save($model);
        }

        return $models;
    }

    /**
     * Attach a collection of models to the parent instance without raising any events to the parent model.
     *
     * @param iterable $models
     *
     * @return iterable
     */
    public function saveManyQuietly($models) {
        return CModel::withoutEvents(function () use ($models) {
            return $this->saveMany($models);
        });
    }

    /**
     * Create a new instance of the related model.
     *
     * @param array $attributes
     *
     * @return CModel
     */
    public function create(array $attributes = []) {
        return c::tap($this->related->newInstance($attributes), function ($instance) {
            $this->setForeignAttributesForCreate($instance);

            $instance->save();
        });
    }

    /**
     * Create a new instance of the related model without raising any events to the parent model.
     *
     * @param array $attributes
     *
     * @return \CModel
     */
    public function createQuietly(array $attributes = []) {
        return CModel::withoutEvents(function () use ($attributes) {
            return $this->create($attributes);
        });
    }

    /**
     * Create a new instance of the related model. Allow mass-assignment.
     *
     * @param array $attributes
     *
     * @return \CModel
     */
    public function forceCreate(array $attributes = []) {
        $attributes[$this->getForeignKeyName()] = $this->getParentKey();

        return $this->related->forceCreate($attributes);
    }

    /**
     * Create a new instance of the related model with mass assignment without raising model events.
     *
     * @param array $attributes
     *
     * @return \CModel
     */
    public function forceCreateQuietly(array $attributes = []) {
        return CModel::withoutEvents(function () use ($attributes) {
            return $this->forceCreate($attributes);
        });
    }

    /**
     * Create a Collection of new instances of the related model.
     *
     * @param array $records
     *
     * @return CModel_Collection
     */
    public function createMany(array $records) {
        $instances = $this->related->newCollection();

        foreach ($records as $record) {
            $instances->push($this->create($record));
        }

        return $instances;
    }

    /**
     * Create a Collection of new instances of the related model without raising any events to the parent model.
     *
     * @param array $records
     *
     * @return \CModel_Collection
     */
    public function createManyQuietly($records) {
        return CModel::withoutEvents(function () use ($records) {
            return $this->createMany($records);
        });
    }

    /**
     * Set the foreign ID for creating a related model.
     *
     * @param CModel $model
     *
     * @return void
     */
    protected function setForeignAttributesForCreate(CModel $model) {
        $model->setAttribute($this->getForeignKeyName(), $this->getParentKey());
    }

    /**
     * Add the constraints for a relationship query.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        if ($query->getQuery()->from == $parentQuery->getQuery()->from) {
            return $this->getRelationExistenceQueryForSelfRelation($query, $parentQuery, $columns);
        }

        return parent::getRelationExistenceQuery($query, $parentQuery, $columns);
    }

    /**
     * Add the constraints for a relationship query on the same table.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQueryForSelfRelation(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        $query->from($query->getModel()->getTable() . ' as ' . $hash = $this->getRelationCountHash());

        $query->getModel()->setTable($hash);

        return $query->select($columns)->whereColumn(
            $this->getQualifiedParentKeyName(),
            '=',
            $hash . '.' . $this->getForeignKeyName()
        );
    }

    /**
     * Get the key for comparing against the parent key in "has" query.
     *
     * @return string
     */
    public function getExistenceCompareKey() {
        return $this->getQualifiedForeignKeyName();
    }

    /**
     * Get the key value of the parent's local key.
     *
     * @return mixed
     */
    public function getParentKey() {
        return $this->parent->getAttribute($this->localKey);
    }

    /**
     * Get the fully qualified parent key name.
     *
     * @return string
     */
    public function getQualifiedParentKeyName() {
        return $this->parent->qualifyColumn($this->localKey);
    }

    /**
     * Get the plain foreign key.
     *
     * @return string
     */
    public function getForeignKeyName() {
        $segments = explode('.', $this->getQualifiedForeignKeyName());

        return end($segments);
    }

    /**
     * Get the foreign key for the relationship.
     *
     * @return string
     */
    public function getQualifiedForeignKeyName() {
        return $this->foreignKey;
    }

    /**
     * Get the local key for the relationship.
     *
     * @return string
     */
    public function getLocalKeyName() {
        return $this->localKey;
    }
}

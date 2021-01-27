<?php

abstract class CModel_Relation_MorphOneOrMany extends CModel_Relation_HasOneOrMany {
    /**
     * The foreign key type for the relationship.
     *
     * @var string
     */
    protected $morphType;

    /**
     * The class name of the parent model.
     *
     * @var string
     */
    protected $morphClass;

    /**
     * Create a new morph one or many relationship instance.
     *
     * @param CModel_Query $query
     * @param CModel       $parent
     * @param string       $type
     * @param string       $id
     * @param string       $localKey
     *
     * @return void
     */
    public function __construct(CModel_Query $query, CModel $parent, $type, $id, $localKey) {
        $this->morphType = $type;

        $this->morphClass = $parent->getMorphClass();
        parent::__construct($query, $parent, $id, $localKey);
    }

    /**
     * Set the base constraints on the relation query.
     *
     * @return void
     */
    public function addConstraints() {
        if (static::$constraints) {
            parent::addConstraints();

            $this->query->where($this->morphType, $this->morphClass);
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
        parent::addEagerConstraints($models);

        $this->query->where($this->morphType, $this->morphClass);
    }

    /**
     * Attach a model instance to the parent model.
     *
     * @param CModel $model
     *
     * @return CModel
     */
    public function save(CModel $model) {
        $model->setAttribute($this->getMorphType(), $this->morphClass);

        return parent::save($model);
    }

    /**
     * Set the foreign ID and type for creating a related model.
     *
     * @param CModel $model
     *
     * @return void
     */
    protected function setForeignAttributesForCreate(CModel $model) {
        $model->{$this->getForeignKeyName()} = $this->getParentKey();

        $model->{$this->getMorphType()} = $this->morphClass;
    }

    /**
     * Get the relationship query.
     *
     * @param CModel_Query $query
     * @param CModel_Query $parentQuery
     * @param array|mixed  $columns
     *
     * @return CModel_Query
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        return parent::getRelationExistenceQuery($query, $parentQuery, $columns)->where(
            $this->morphType,
            $this->morphClass
        );
    }

    /**
     * Get the foreign key "type" name.
     *
     * @return string
     */
    public function getQualifiedMorphType() {
        return $this->morphType;
    }

    /**
     * Get the plain morph type name without the table.
     *
     * @return string
     */
    public function getMorphType() {
        return carr::last(explode('.', $this->morphType));
    }

    /**
     * Get the class name of the parent model.
     *
     * @return string
     */
    public function getMorphClass() {
        return $this->morphClass;
    }
}

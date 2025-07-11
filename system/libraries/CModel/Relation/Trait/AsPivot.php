<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CModel_Relation_Trait_AsPivot {
    /**
     * The parent model of the relationship.
     *
     * @var CModel
     */
    public $pivotParent;

    /**
     * The name of the foreign key column.
     *
     * @var string
     */
    protected $foreignKey;

    /**
     * The name of the "other key" column.
     *
     * @var string
     */
    protected $relatedKey;

    /**
     * Create a new pivot model instance.
     *
     * @param CModel $parent
     * @param array  $attributes
     * @param string $table
     * @param bool   $exists
     *
     * @return static
     */
    public static function fromAttributes(CModel $parent, $attributes, $table, $exists = false) {
        $instance = new static();

        $instance->timestamps = $instance->hasTimestampAttributes($attributes);

        // The pivot model is a "dynamic" model since we will set the tables dynamically
        // for the instance. This allows it work for any intermediate tables for the
        // many to many relationship that are defined by this developer's classes.
        $instance->setConnection($parent->getConnectionName())
            ->setTable($table)
            ->forceFill($attributes)
            ->syncOriginal();

        // We store off the parent instance so we will access the timestamp column names
        // for the model, since the pivot model timestamps aren't easily configurable
        // from the developer's point of view. We can use the parents to get these.
        $instance->pivotParent = $parent;

        $instance->exists = $exists;

        return $instance;
    }

    /**
     * Create a new pivot model from raw values returned from a query.
     *
     * @param CModel $parent
     * @param array  $attributes
     * @param string $table
     * @param bool   $exists
     *
     * @return static
     */
    public static function fromRawAttributes(CModel $parent, $attributes, $table, $exists = false) {
        $instance = static::fromAttributes($parent, [], $table, $exists);

        $instance->timestamps = $instance->hasTimestampAttributes($attributes);

        $instance->setRawAttributes($attributes, true);

        return $instance;
    }

    /**
     * Set the keys for a select query.
     *
     * @param CModel_Query $query
     *
     * @return CModel_Query
     */
    protected function setKeysForSelectQuery($query) {
        if (isset($this->attributes[$this->getKeyName()])) {
            return parent::setKeysForSelectQuery($query);
        }

        $query->where($this->foreignKey, $this->getOriginal(
            $this->foreignKey,
            $this->getAttribute($this->foreignKey)
        ));

        return $query->where($this->relatedKey, $this->getOriginal(
            $this->relatedKey,
            $this->getAttribute($this->relatedKey)
        ));
    }

    /**
     * Set the keys for a save update query.
     *
     * @param CModel_Query $query
     *
     * @return CModel_Query
     */
    protected function setKeysForSaveQuery(CModel_Query $query) {
        return $this->setKeysForSelectQuery($query);
    }

    /**
     * Delete the pivot model record from the database.
     *
     * @return int
     */
    public function delete() {
        if (isset($this->attributes[$this->getKeyName()])) {
            return (int) parent::delete();
        }
        if ($this->fireModelEvent('deleting') === false) {
            return 0;
        }
        $this->touchOwners();

        return c::tap($this->getDeleteQuery()->delete(), function () {
            $this->exists = false;

            $this->fireModelEvent('deleted', false);
        });
    }

    /**
     * Get the query builder for a delete operation on the pivot.
     *
     * @return CModel_Query
     */
    protected function getDeleteQuery() {
        return $this->newQueryWithoutRelationships()->where([
            $this->foreignKey => $this->getOriginal($this->foreignKey, $this->getAttribute($this->foreignKey)),
            $this->relatedKey => $this->getOriginal($this->relatedKey, $this->getAttribute($this->relatedKey)),
        ]);
    }

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable() {
        if (!isset($this->table)) {
            $this->setTable(str_replace(
                '\\',
                '',
                cstr::snake(cstr::singular(c::classBasename($this)))
            ));
        }

        return $this->table;
    }

    /**
     * Get the foreign key column name.
     *
     * @return string
     */
    public function getForeignKey() {
        return $this->foreignKey;
    }

    /**
     * Get the "related key" column name.
     *
     * @return string
     */
    public function getRelatedKey() {
        return $this->relatedKey;
    }

    /**
     * Get the "related key" column name.
     *
     * @return string
     */
    public function getOtherKey() {
        return $this->getRelatedKey();
    }

    /**
     * Set the key names for the pivot model instance.
     *
     * @param string $foreignKey
     * @param string $relatedKey
     *
     * @return $this
     */
    public function setPivotKeys($foreignKey, $relatedKey) {
        $this->foreignKey = $foreignKey;
        $this->relatedKey = $relatedKey;

        return $this;
    }

    /**
     * Determine if the pivot model or given attributes has timestamp attributes.
     *
     * @param $attributes array|null
     *
     * @return bool
     */
    public function hasTimestampAttributes($attributes = null) {
        return array_key_exists($this->getCreatedAtColumn(), $attributes ? $attributes : $this->attributes);
    }

    /**
     * Get the name of the "created at" column.
     *
     * @return string
     */
    public function getCreatedAtColumn() {
        return $this->pivotParent ? $this->pivotParent->getCreatedAtColumn() : parent::getCreatedAtColumn();
    }

    /**
     * Get the name of the "updated at" column.
     *
     * @return string
     */
    public function getUpdatedAtColumn() {
        return $this->pivotParent ? $this->pivotParent->getUpdatedAtColumn() : parent::getUpdatedAtColumn();
    }

    /**
     * Get the queueable identity for the entity.
     *
     * @return mixed
     */
    public function getQueueableId() {
        if (isset($this->attributes[$this->getKeyName()])) {
            return $this->getKey();
        }

        return sprintf(
            '%s:%s:%s:%s',
            $this->foreignKey,
            $this->getAttribute($this->foreignKey),
            $this->relatedKey,
            $this->getAttribute($this->relatedKey)
        );
    }

    /**
     * Get a new query to restore one or more models by their queueable IDs.
     *
     * @param int[]|string[]|string $ids
     *
     * @return CModel_Query
     */
    public function newQueryForRestoration($ids) {
        if (is_array($ids)) {
            return $this->newQueryForCollectionRestoration($ids);
        }
        if (!cstr::contains($ids, ':')) {
            return parent::newQueryForRestoration($ids);
        }
        $segments = explode(':', $ids);

        return $this->newQueryWithoutScopes()
            ->where($segments[0], $segments[1])
            ->where($segments[2], $segments[3]);
    }

    /**
     * Get a new query to restore multiple models by their queueable IDs.
     *
     * @param int[]|string[] $ids
     *
     * @return CModel_Query
     */
    protected function newQueryForCollectionRestoration(array $ids) {
        $ids = array_values($ids);
        if (!cstr::contains($ids[0], ':')) {
            return parent::newQueryForRestoration($ids);
        }
        $query = $this->newQueryWithoutScopes();
        foreach ($ids as $id) {
            $segments = explode(':', $id);
            $query->orWhere(function ($query) use ($segments) {
                return $query->where($segments[0], $segments[1])
                    ->where($segments[2], $segments[3]);
            });
        }

        return $query;
    }

    /**
     * Unset all the loaded relations for the instance.
     *
     * @return $this
     */
    public function unsetRelations() {
        $this->pivotParent = null;
        $this->relations = [];

        return $this;
    }
}

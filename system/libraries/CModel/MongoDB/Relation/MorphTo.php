<?php

class CModel_MongoDB_Relation_MorphTo extends CModel_Relation_MorphTo {
    /**
     * @inheritdoc
     */
    public function addConstraints() {
        if (static::$constraints) {
            // For belongs to relationships, which are essentially the inverse of has one
            // or has many relationships, we need to actually query on the primary key
            // of the related models matching on the foreign key that's on a parent.
            $this->query->where($this->getOwnerKey(), '=', $this->parent->{$this->foreignKey});
        }
    }

    /**
     * @inheritdoc
     */
    protected function getResultsByType($type) {
        $instance = $this->createModelByType($type);

        $key = $instance->getKeyName();

        $query = $instance->newQuery();

        return $query->whereIn($key, $this->gatherKeysByType($type, $instance->getKeyType()))->get();
    }

    /**
     * Get the owner key with backwards compatible support.
     *
     * @return string
     */
    public function getOwnerKey() {
        return property_exists($this, 'ownerKey') ? $this->ownerKey : $this->otherKey;
    }

    /**
     * Get the name of the "where in" method for eager loading.
     *
     * @param CModel $model
     * @param string $key
     *
     * @return string
     */
    protected function whereInMethod(CModel $model, $key) {
        return 'whereIn';
    }
}

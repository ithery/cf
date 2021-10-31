<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 21, 2019, 9:22:47 PM
 */
class CModel_MongoDB_Relation_BelongsTo extends CModel_Relation_BelongsTo {
    /**
     * Get the key for comparing against the parent key in "has" query.
     *
     * @return string
     */
    public function getHasCompareKey() {
        return $this->getOwnerKey();
    }

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
    public function addEagerConstraints(array $models) {
        // We'll grab the primary key name of the related models since it could be set to
        // a non-standard name and not "id". We will then construct the constraint for
        // our eagerly loading query so it returns the proper models from execution.
        $key = $this->getOwnerKey();
        $this->query->whereIn($key, $this->getEagerModelKeys($models));
    }

    /**
     * @inheritdoc
     */
    public function getRelationExistenceQuery(CModel_Query $query, CModel_Query $parentQuery, $columns = ['*']) {
        return $query;
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
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $key
     *
     * @return string
     */
    protected function whereInMethod(CModel $model, $key) {
        return 'whereIn';
    }
}

<?php

trait CModel_Trait_Relationships_ConcatenatesRelationships {
    /**
     * Prepare a has-one-deep or has-many-deep relationship from existing relationships.
     *
     * @param \CModel_Relation[] $relations
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromRelations(array $relations) {
        if (is_array($relations[0])) {
            $relations = $relations[0];
        }

        $related = null;
        $through = [];
        $foreignKeys = [];
        $localKeys = [];

        foreach ($relations as $i => $relation) {
            $method = $this->hasOneOrManyDeepRelationMethod($relation);

            list($through, $foreignKeys, $localKeys) = $this->$method($relation, $through, $foreignKeys, $localKeys);

            if ($i === count($relations) - 1) {
                $related = get_class($relation->getRelated());

                if ((new $related())->getTable() !== $relation->getRelated()->getTable()) {
                    $related .= ' from ' . $relation->getRelated()->getTable();
                }
            } else {
                $through[] = $this->hasOneOrManyThroughParent($relation, $relations[$i + 1]);
            }
        }

        return [$related, $through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing belongs-to relationship.
     *
     * @param \CModel_Relation_BelongsTo $relation
     * @param \CModel[]                  $through
     * @param array                      $foreignKeys
     * @param array                      $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromBelongsTo(CModel_Relation_BelongsTo $relation, array $through, array $foreignKeys, array $localKeys) {
        $foreignKeys[] = $relation->getOwnerKeyName();

        $localKeys[] = $relation->getForeignKeyName();

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing belongs-to-many relationship.
     *
     * @param \CModel_Relation_BelongsToMany $relation
     * @param \CModel[]                      $through
     * @param array                          $foreignKeys
     * @param array                          $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromBelongsToMany(CModel_Relation_BelongsToMany $relation, array $through, array $foreignKeys, array $localKeys) {
        $through[] = $relation->getTable();

        $foreignKeys[] = $relation->getForeignPivotKeyName();
        $foreignKeys[] = $relation->getRelatedKeyName();

        $localKeys[] = $relation->getParentKeyName();
        $localKeys[] = $relation->getRelatedPivotKeyName();

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing has-one or has-many relationship.
     *
     * @param \CModel_Relation_HasOneOrMany $relation
     * @param \CModel[]                     $through
     * @param array                         $foreignKeys
     * @param array                         $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromHasOneOrMany(CModel_Relation_HasOneOrMany $relation, array $through, array $foreignKeys, array $localKeys) {
        $foreignKeys[] = $relation->getQualifiedForeignKeyName();

        $localKeys[] = $relation->getLocalKeyName();

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing has-many-through relationship.
     *
     * @param \CModel_Relation_HasManyThrough $relation
     * @param \CModel[]                       $through
     * @param array                           $foreignKeys
     * @param array                           $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromHasManyThrough(CModel_Relation_HasManyThrough $relation, array $through, array $foreignKeys, array $localKeys) {
        $through[] = get_class($relation->getParent());

        $foreignKeys[] = $relation->getFirstKeyName();
        $foreignKeys[] = $relation->getForeignKeyName();

        $localKeys[] = $relation->getLocalKeyName();
        $localKeys[] = $relation->getSecondLocalKeyName();

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing has-many-deep relationship.
     *
     * @param \CModel_Relation_HasManyDeep $relation
     * @param \CModel[]                    $through
     * @param array                        $foreignKeys
     * @param array                        $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromHasManyDeep(CModel_Relation_HasManyDeep $relation, array $through, array $foreignKeys, array $localKeys) {
        foreach ($relation->getThroughParents() as $throughParent) {
            $segments = explode(' as ', $throughParent->getTable());

            $class = get_class($throughParent);

            if (isset($segments[1])) {
                $class .= ' as ' . $segments[1];
            } elseif ($throughParent instanceof CModel_Relation_Pivot) {
                $class = $throughParent->getTable();
            }

            $through[] = $class;
        }

        $foreignKeys = array_merge($foreignKeys, $relation->getForeignKeys());

        $localKeys = array_merge($localKeys, $relation->getLocalKeys());

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing morph-one or morph-many relationship.
     *
     * @param \CModel_Relation_MorphOneOrMany $relation
     * @param \CModel[]                       $through
     * @param array                           $foreignKeys
     * @param array                           $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromMorphOneOrMany(CModel_Relation_MorphOneOrMany $relation, array $through, array $foreignKeys, array $localKeys) {
        $foreignKeys[] = [$relation->getQualifiedMorphType(), $relation->getQualifiedForeignKeyName()];

        $localKeys[] = $relation->getLocalKeyName();

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Prepare a has-one-deep or has-many-deep relationship from an existing morph-to-many relationship.
     *
     * @param \CModel_Relation_MorphToMany $relation
     * @param \CModel[]                    $through
     * @param array                        $foreignKeys
     * @param array                        $localKeys
     *
     * @return array
     */
    protected function hasOneOrManyDeepFromMorphToMany(CModel_Relation_MorphToMany $relation, array $through, array $foreignKeys, array $localKeys) {
        $through[] = $relation->getTable();

        if ($relation->getInverse()) {
            $foreignKeys[] = $relation->getForeignPivotKeyName();
            $foreignKeys[] = $relation->getRelatedKeyName();

            $localKeys[] = $relation->getParentKeyName();
            $localKeys[] = [$relation->getMorphType(), $relation->getRelatedPivotKeyName()];
        } else {
            $foreignKeys[] = [$relation->getMorphType(), $relation->getForeignPivotKeyName()];
            $foreignKeys[] = $relation->getRelatedKeyName();

            $localKeys[] = $relation->getParentKeyName();
            $localKeys[] = $relation->getRelatedPivotKeyName();
        }

        return [$through, $foreignKeys, $localKeys];
    }

    /**
     * Get the relationship method name.
     *
     * @param \CModel_Relation $relation
     *
     * @return string
     */
    protected function hasOneOrManyDeepRelationMethod(CModel_Relation $relation) {
        $classes = [
            CModel_Relation_BelongsTo::class,
            CModel_Relation_HasManyDeep::class,
            CModel_Relation_HasManyThrough::class,
            CModel_Relation_MorphOneOrMany::class,
            CModel_Relation_HasOneOrMany::class,
            CModel_Relation_MorphToMany::class,
            CModel_Relation_BelongsToMany::class,
        ];

        foreach ($classes as $class) {
            if ($relation instanceof $class) {
                return 'hasOneOrManyDeepFrom' . c::classBasename($class);
            }
        }

        throw new RuntimeException('This relationship is not supported.'); // @codeCoverageIgnore
    }

    /**
     * Prepare the through parent class from an existing relationship and its successor.
     *
     * @param \CModel_Relation $relation
     * @param \CModel_Relation $successor
     *
     * @return string
     */
    protected function hasOneOrManyThroughParent(CModel_Relation $relation, CModel_Relation $successor) {
        $through = get_class($relation->getRelated());

        if (get_class($relation->getRelated()) === get_class($successor->getParent())) {
            $table = $successor->getParent()->getTable();
            $segments = explode(' as ', $table);

            if (isset($segments[1])) {
                $through .= ' as ' . $segments[1];
            } else {
                $through = $table;
            }
        }

        return $through;
    }
}

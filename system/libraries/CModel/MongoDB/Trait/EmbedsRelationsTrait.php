<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 21, 2019, 9:22:00 PM
 */
trait CModel_MongoDB_Trait_EmbedsRelationsTrait {
    /**
     * Define an embedded one-to-many relationship.
     *
     * @param string $related
     * @param string $localKey
     * @param string $foreignKey
     * @param string $relation
     *
     * @return CModel_MongoDB_Relation_EmbedsMany
     */
    protected function embedsMany($related, $localKey = null, $foreignKey = null, $relation = null) {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if ($relation === null) {
            list(, $caller) = debug_backtrace(false);
            $relation = $caller['function'];
        }
        if ($localKey === null) {
            $localKey = $relation;
        }
        if ($foreignKey === null) {
            $foreignKey = cstr::snake(c::classBasename($this));
        }
        $query = $this->newQuery();
        $instance = new $related;
        return new CModel_MongoDB_Relation_EmbedsMany($query, $this, $instance, $localKey, $foreignKey, $relation);
    }

    /**
     * Define an embedded one-to-many relationship.
     *
     * @param string $related
     * @param string $localKey
     * @param string $foreignKey
     * @param string $relation
     *
     * @return CModel_MongoDB_Relation_EmbedsOne
     */
    protected function embedsOne($related, $localKey = null, $foreignKey = null, $relation = null) {
        // If no relation name was given, we will use this debug backtrace to extract
        // the calling method's name and use that as the relationship name as most
        // of the time this will be what we desire to use for the relationships.
        if ($relation === null) {
            list(, $caller) = debug_backtrace(false);
            $relation = $caller['function'];
        }
        if ($localKey === null) {
            $localKey = $relation;
        }
        if ($foreignKey === null) {
            $foreignKey = cstr::snake(c::classBasename($this));
        }
        $query = $this->newQuery();
        $instance = new $related;
        return new CModel_MongoDB_Relation_EmbedsOne($query, $this, $instance, $localKey, $foreignKey, $relation);
    }
}

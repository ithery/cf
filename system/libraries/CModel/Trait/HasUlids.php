<?php

trait CModel_Trait_HasUlids {
    /**
     * Initialize the trait.
     *
     * @return void
     */
    public function initializeHasUlids() {
        $this->usesUniqueIds = true;
    }

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds() {
        return [$this->getKeyName()];
    }

    /**
     * Generate a new ULID for the model.
     *
     * @return string
     */
    public function newUniqueId() {
        return strtolower((string) cstr::ulid());
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  \CModel|\CModel_Relation<*, *, *>  $query
     * @param mixed       $value
     * @param null|string $field
     *
     * @throws \CModel_Exception_ModelNotFoundException
     *
     * @return \CModel_Query
     */
    public function resolveRouteBindingQuery($query, $value, $field = null) {
        if ($field && in_array($field, $this->uniqueIds()) && !cstr::isUlid($value)) {
            throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($this), $value);
        }

        if (!$field && in_array($this->getRouteKeyName(), $this->uniqueIds()) && !cstr::isUlid($value)) {
            throw (new CModel_Exception_ModelNotFoundException())->setModel(get_class($this), $value);
        }

        return parent::resolveRouteBindingQuery($query, $value, $field);
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType() {
        if (in_array($this->getKeyName(), $this->uniqueIds())) {
            return 'string';
        }

        return $this->keyType;
    }

    /**
     * Get the value indicating whether the IDs are incrementing.
     *
     * @return bool
     */
    public function getIncrementing() {
        if (in_array($this->getKeyName(), $this->uniqueIds())) {
            return false;
        }

        return $this->incrementing;
    }
}

<?php

trait CModel_Deleted_DeletedTrait {
    /**
     * Get the name of the "deleted at" column.
     *
     * @return string
     */
    public function getDeletedAtColumn() {
        /** @var CModel static */
        return defined('static::DELETED') ? static::DELETED : 'deleted';
    }

    /**
     * Get the fully qualified "deleted at" column.
     *
     * @return string
     */
    public function getQualifiedDeletedAtColumn() {
        return $this->getTable() . '.' . $this->getDeletedAtColumn();
    }
}

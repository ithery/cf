<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

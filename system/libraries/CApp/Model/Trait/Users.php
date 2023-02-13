<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
trait CApp_Model_Trait_Users {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'user_id';
        $this->table = 'users';
        $this->guarded = ['user_id'];
    }

    /**
     * @return CModel_Relation_BelongsTo
     */
    public function org() {
        return $this->belongsTo(CApp_Model_Org::class);
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function role() {
        return $this->belongsTo(c::app()->auth()->getRoleModelClass())->withTrashed();
    }
}

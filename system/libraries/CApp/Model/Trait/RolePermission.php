<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
trait CApp_Model_Trait_RolePermission {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_permission_id';
        $this->table = 'role_permission';
        $this->guarded = ['role_permission_id'];
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function org() {
        return $this->belongsTo(CApp_Model_Org::class)->withTrashed();
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function roles() {
        return $this->belongsTo(CApp_Model_Roles::class)->withTrashed();
    }
}

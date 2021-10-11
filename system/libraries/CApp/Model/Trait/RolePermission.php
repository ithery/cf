<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2018, 1:34:29 AM
 */
trait CApp_Model_Trait_RolePermission {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_permission_id';
        $this->table = 'role_permission';
        $this->guarded = ['role_permission_id'];
    }

    /**
     * @return CModel_Relation_BelongsTo
     */
    public function org() {
        return $this->belongsTo(CApp_Model_Org::class);
    }

    /**
     * @return CModel_Relation_BelongsTo
     */
    public function roles() {
        return $this->belongsTo(CApp_Model_Roles::class);
    }
}

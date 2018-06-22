<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2018, 1:34:29 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_RolePermission {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_permission_id';
        $this->table = 'role_permission';
        $this->guarded = array('role_permission_id');
    }

    /**
     * 
     * @return CModel_Relation_BelongsTo
     */
    public function org() {
        return $this->belongsTo('CApp_Model_Org');
    }

    /**
     * 
     * @return CModel_Relation_BelongsTo
     */
    public function roles() {
        return $this->belongsTo('CMModel_Roles');
    }

}

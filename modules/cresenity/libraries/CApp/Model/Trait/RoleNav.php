<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2018, 1:33:26 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_RoleNav {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_nav_id';
        $this->table = 'role_nav';
        $this->guarded = array('role_nav_id');
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

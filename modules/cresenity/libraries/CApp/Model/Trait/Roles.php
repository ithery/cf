<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 11:36:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_Roles {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_id';
        $this->table = 'roles';
        $this->guarded = array('role_id');
    }

    /**
     * 
     * @return CModel_Relation_BelongsTo
     */
    public function org() {
        return $this->belongsTo('CMModel_Org');
    }

}

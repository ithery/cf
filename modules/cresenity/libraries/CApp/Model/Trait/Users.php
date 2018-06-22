<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 11:36:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_Users {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'user_id';
        $this->table = 'users';
        $this->guarded = array('user_id');
    }

    /**
     * 
     * @return CModel_Relation_BelongsTo
     */
    public function org() {
        return $this->belongsTo('CApp_Model_Org');
    }

}

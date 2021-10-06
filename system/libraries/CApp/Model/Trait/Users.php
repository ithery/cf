<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 11:36:57 PM
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
        return $this->belongsTo('CApp_Model_Org');
    }

    /**
     * @return CModel_Relation_BelongsTo
     */
    public function role() {
        return $this->belongsTo('CApp_Model_Roles');
    }
}

<?php

class CAuth_Model_UsersModel extends CModel {
    use CAuth_Concern_AuthenticatableTrait,
        CAuth_Concern_AuthorizableTrait;

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

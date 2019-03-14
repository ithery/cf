<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 19, 2018, 11:36:57 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_Roles {

    use CModel_Nested_NestedTrait,
        CModel_SoftDelete_Trait;

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
        return $this->belongsTo('CApp_Model_Org');
    }

    public function rolePermission() {
        return $this->hasMany('CApp_Model_RolePermission', 'role_id', 'role_id');
    }

    public function getDescendantsTree($rootId = null, $orgId = null) {
        $root = $this;
        if (strlen($rootId) > 0) {
            $root = $this->find($rootId);
        }

        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }

        $root = $root->descendants();

        if (strlen($orgId) > 0) {
            $root = $root->where(function($query) use ($orgId) {
                        $query->where('org_id', '=', $orgId)->orWhereNull('org_id');
                    })->where('status', '>', 0);
        }

        $tree = $root->get()->toTree();

        return $tree;
    }

}

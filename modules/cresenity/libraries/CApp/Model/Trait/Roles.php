<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 19, 2018, 11:36:57 PM
 */
trait CApp_Model_Trait_Roles {
    use CModel_Nested_NestedTrait;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_id';
        $this->table = 'roles';
        $this->guarded = ['role_id'];
    }

    /**
     * @return CModel_Relation_BelongsTo
     */
    public function org() {
        return $this->belongsTo(CApp_Model_Org::class);
    }

    public function rolePermission() {
        return $this->hasMany(CApp_Model_RolePermission::class, 'role_id', 'role_id');
    }

    public function getDescendantsTree($rootId = null, $orgId = null, $type = null) {
        $root = $this;
        if (strlen($rootId) > 0) {
            $root = $this->find($rootId);
        }

        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }

        $root = $root->descendants();
        if (strlen($orgId) > 0) {
            $root = $root->where(function ($query) use ($orgId) {
                $query->where('org_id', '=', $orgId)->orWhereNull('org_id');
            })->where('status', '>', 0);
        }
        if (strlen($type) > 0) {
            $root = $root->where('type', '=', $type);
        }
        $tree = $root->get()->toTree();

        return $tree;
    }
}

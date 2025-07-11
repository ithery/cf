<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      string                                                    $name
 * @property      null|string                                               $createdby
 * @property      null|string                                               $updatedby
 * @property      null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $created
 * @property      null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $updated
 * @property      int                                                       $status
 * @property      int                                                       $depth
 * @property-read int                                                       $role_id
 *
 * @method static CModel_Collection byAccess(string $permitWithoutWildcard)
 * @method static CModel_Collection byAnyAccess(string $permitWithoutWildcard)
 */
trait CApp_Model_Trait_Roles {
    use CModel_Nested_NestedTrait;

    /**
     * @var null|\CCollection
     */
    private $cachePermissions;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_id';
        $this->table = 'roles';
        $this->guarded = ['role_id'];
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function org() {
        return $this->belongsTo(CApp_Model_Org::class)->withTrashed();
    }

    public function rolePermission() {
        return $this->hasMany(c::app()->auth()->getRolePermisionModelClass());
    }

    public function scopeWhereHasPermission(CModel_Query $q, $permission) {
        $q->whereHas('rolePermission', function ($q) use ($permission) {
            $q->where('name', '=', $permission);
        });
    }

    public function getDescendantsTree($rootId = null, $orgId = null, $type = null) {
        /** @var CApp_Model_Roles $this */
        $root = $this;
        if (strlen($rootId) > 0) {
            $root = $this->find($rootId);
        }

        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }

        $root = $root->descendants();
        /** @var CModel_Nested_Relation_Descendants $root */
        if (strlen($orgId) > 0) {
            $root = $root->where(function ($query) use ($orgId) {
                $query->where('org_id', '=', $orgId)->orWhereNull('org_id');
            })->where('status', '>', 0);
        }
        if (is_callable($type)) {
            call_user_func_array($type, [$root]);
        } elseif (is_array($type)) {
            foreach ($type as $k => $v) {
                $root = $root->where($k, '=', $v);
            }
        } else {
            if (strlen($type) > 0) {
                $root = $root->where('type', '=', $type);
            }
        }
        $tree = $root->get()->toTree();

        return $tree;
    }

    /**
     * @return int
     */
    public function getRoleId() {
        return $this->getKey();
    }

    /**
     * @param string $permit
     * @param bool   $cache
     *
     * @return bool
     */
    public function hasAccess($permit, $cache = true) {
        if (!is_string($permit)) {
            throw new Exception('permit must be string when passed on hasAccess');
        }
        if (!$cache || $this->cachePermissions === null) {
            $this->cachePermissions = $this->rolePermission()->pluck('name')
                ->filter();
        }

        return $this->filterWildcardAccess($this->cachePermissions, $permit);
    }

    /**
     * Permissions can be checked based on wildcards
     * using the * character to match any of a set of permissions.
     *
     * @param array  $permissions
     * @param string $permit
     *
     * @return bool
     */
    protected function filterWildcardAccess(CCollection $permissions, $permit) {
        return c::collect($permissions)->filter(function ($value) use ($permit) {
            return cstr::is($permit, $value);
        })->isNotEmpty();
    }

    /**
     * This method will grant access if any permission passes the check.
     *
     * @param string|iterable $permissions
     * @param bool            $cache
     *
     * @return bool
     */
    public function hasAnyAccess($permissions, $cache = true) {
        if (empty($permissions)) {
            return true;
        }

        return c::collect($permissions)
            ->map(function ($permit) use ($cache) {
                return $this->hasAccess($permit, $cache);
            })
            ->filter(function ($result) {
                return $result === true;
            })
            ->isNotEmpty();
    }

    /**
     * Query Scope for retreiving users by a certain permission
     * The * character usage is not implemented.
     *
     * @param \CModel_Query $builder
     * @param string        $permitWithoutWildcard
     *
     * @return \CModel_Query
     */
    public function scopeByAccess(CModel_Query $query, $permitWithoutWildcard) {
        if (empty($permitWithoutWildcard)) {
            return $query->whereRaw('1=0');
        }

        return $this->scopeByAnyAccess($query, $permitWithoutWildcard);
    }

    /**
     * Query Scope for retreiving users by any permissions
     * The * character usage is not implemented.
     *
     * @param \CModel_Query   $builder
     * @param string|iterable $permitsWithoutWildcard
     *
     * @return \CModel_Query
     */
    public function scopeByAnyAccess(CModel_Query $query, $permitsWithoutWildcard) {
        $permits = c::collect($permitsWithoutWildcard);

        if ($permits->isEmpty()) {
            return $query->whereRaw('1=0');
        }

        return $query->whereHas('rolePermission', function ($q) use ($permits) {
            $permits->each(function ($permit) use ($q) {
                $q->orWhere('name', '=', $permit);
            });
        });
    }

    /**
     * @return self
     */
    public function clearCachePermission() {
        $this->cachePermissions = null;

        return $this;
    }
}

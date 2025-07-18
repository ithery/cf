<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property null|string                                               $createdby
 * @property null|string                                               $updatedby
 * @property null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $created
 * @property null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $updated
 * @property int                                                       $status
 */
trait CApp_Model_Trait_RoleNav {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'role_nav_id';
        $this->table = 'role_nav';
        $this->guarded = ['role_nav_id'];
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function org() {
        return $this->belongsTo(CApp_Model_Org::class)->withTrashed();
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function roles() {
        return $this->belongsTo(CApp_Model_Roles::class)->withTrashed();
    }
}

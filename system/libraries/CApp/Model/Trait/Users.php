<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property null|string                                               $createdby
 * @property null|string                                               $updatedby
 * @property null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $created
 * @property null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $updated
 * @property int                                                       $status
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
        return $this->belongsTo(CApp_Model_Org::class);
    }

    /**
     * @return CModel_Relation_BelongsTo|CModel_Query
     */
    public function role() {
        return $this->belongsTo(c::app()->auth()->getRoleModelClass())->withTrashed();
    }
}

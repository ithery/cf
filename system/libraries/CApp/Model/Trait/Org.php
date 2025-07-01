<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      null|string                                               $createdby
 * @property      null|string                                               $updatedby
 * @property      null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $created
 * @property      null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $updated
 * @property      int                                                       $status
 * @property-read int                                                       $org_id
 */
trait CApp_Model_Trait_Org {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'org_id';
        $this->table = 'org';
        $this->guarded = ['org_id'];
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property      string  $createdby
 * @property      string  $updatedby
 * @property      CCarbon $created
 * @property      CCarbon $updated
 * @property      int     $status
 * @property-read int     $org_id
 */
trait CApp_Model_Trait_Org {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'org_id';
        $this->table = 'org';
        $this->guarded = ['org_id'];
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
trait CApp_Model_Trait_SysCounter {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'sys_counter_id';
        $this->table = 'sys_counter';
        $this->guarded = ['sys_counter_id'];
    }
}

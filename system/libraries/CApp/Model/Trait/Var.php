<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
trait CApp_Model_Trait_Var {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'var_id';
        $this->table = 'var';
        $this->guarded = ['var_id'];
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property null|string                                               $createdby
 * @property null|string                                               $updatedby
 * @property null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $created
 * @property null|string|CCarbon|\Carbon\Carbon|\CarbonV3\Carbonstring $updated
 * @property int                                                       $status
 */
trait CApp_Model_Trait_Var {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'var_id';
        $this->table = 'var';
        $this->guarded = ['var_id'];
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @property string  $createdby
 * @property string  $updatedby
 * @property CCarbon $created
 * @property CCarbon $updated
 * @property int     $status
 */
trait CApp_Model_Trait_LogRequest {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_request_id';
        $this->table = 'log_request';
        $this->guarded = ['log_request_id'];
    }
}

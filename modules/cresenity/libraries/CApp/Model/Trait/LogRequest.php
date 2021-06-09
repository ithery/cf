<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 10, 2019, 6:11:27 AM
 */
trait CApp_Model_Trait_LogRequest {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_request_id';
        $this->table = 'log_request';
        $this->guarded = ['log_request_id'];
    }
}

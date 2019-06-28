<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 10, 2019, 6:11:27 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_LogRequest {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'log_request_id';
        $this->table = 'log_request';
        $this->guarded = array('log_request_id');
    }

}

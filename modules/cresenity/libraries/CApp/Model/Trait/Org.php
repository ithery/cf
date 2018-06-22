<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2018, 1:29:45 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_Org {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'org_id';
        $this->table = 'org';
        $this->guarded = array('org_id');
    }

}

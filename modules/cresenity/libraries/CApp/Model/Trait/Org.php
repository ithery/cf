<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2018, 1:29:45 AM
 */
trait CApp_Model_Trait_Org {
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->primaryKey = 'org_id';
        $this->table = 'org';
        $this->guarded = ['org_id'];
    }
}

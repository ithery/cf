<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 12, 2018, 8:42:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Model_Trait_Var {

    public function __construct(array $attributes = array()) {
        parent::__construct($attributes);
        $this->primaryKey = 'var_id';
        $this->table = 'var';
        $this->guarded = array('var_id');
    }

}

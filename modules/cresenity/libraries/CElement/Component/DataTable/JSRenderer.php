<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 13, 2019, 3:09:40 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_DataTable_JSRenderer {

    protected $table;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
    }

}

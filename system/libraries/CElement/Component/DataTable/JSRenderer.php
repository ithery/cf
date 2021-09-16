<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 13, 2019, 3:09:40 PM
 */
class CElement_Component_DataTable_JSRenderer {
    protected $table;

    public function __construct(CElement_Component_DataTable $table) {
        $this->table = $table;
    }
}

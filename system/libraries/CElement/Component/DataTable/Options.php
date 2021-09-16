<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 10:04:13 PM
 */
class CElement_Component_DataTable_Options extends CList {
    private $default_options = [
        'bDeferRender' => true,
        'bFilter' => true,
        'bInfo' => true,
        'bPaginate' => true,
        'bLengthChange' => true,
        'height' => false,
    ];

    public function __construct() {
        parent::__construct();
        foreach ($this->default_options as $k => $v) {
            $this->add($k, $v);
        }
    }

    public static function factory() {
        return new CElement_Component_DataTable_Options();
    }
}

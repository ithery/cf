<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Oct 28, 2017, 2:25:21 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Div extends CElement {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CElement_Div($id);
    }

}

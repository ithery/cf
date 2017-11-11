<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Nov 12, 2017, 3:34:27 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Element_H6 extends CElement_Element {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "h6";
    }

}

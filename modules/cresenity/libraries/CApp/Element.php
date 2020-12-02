<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Oct 30, 2020 
 * @license Ittron Global Teknologi
 */
class CApp_Element extends CObservable {

    public function __construct($id = "") {
        parent::__construct($id);
    }

    public function js($indent = 0) {
        $js = parent::js($indent);
        return $js;
    }

}

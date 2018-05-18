<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CWidget extends CElement_Component_Widget {

    public static function factory($id = "") {
        return new CElement_Component_Widget($id);
    }

}

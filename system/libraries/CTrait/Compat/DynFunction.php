<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 9, 2019, 2:40:03 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_DynFunction {
    public function set_function($func) {
        return $this->setFunction($func);
    }

    public function get_function() {
        return $this->getFunction();
    }

    public function add_param($p) {
        return $this->addParam($p);
    }

    public function add_require($p) {
        return $this->addRequire($p);
    }

    public function set_require($p) {
        return $this->setRequire($p);
    }
}

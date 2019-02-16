<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 17, 2019, 2:08:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_DateTime_MaterialDateTime extends CElement_FormInput_DateTime {

    public function __construct($id) {
        parent::__construct($id);
        CManager::instance()->registerModule('bootstrap-4-material-datetimepicker');
    }

    public function build() {
        parent::build();
        $this->addClass('form-control');
    }

}

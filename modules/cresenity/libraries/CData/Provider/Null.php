<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 1:05:11 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CData_Provider_Null extends CData_ProviderAbstract {

    public function data() {
        return array();
    }

}

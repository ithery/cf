<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 1:05:11 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CProvider_Data_Null extends CProvider_DataAbstract {

    public function data() {
        return array();
    }

}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 1:05:11 AM
 */
class CProvider_Data_Null extends CProvider_DataAbstract {
    public function getData() {
        return [];
    }
}

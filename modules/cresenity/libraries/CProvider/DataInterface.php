<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:59:09 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CProvider_DataInterface {

    /**
     * @return array[] array2d
     */
    public function getData();
}

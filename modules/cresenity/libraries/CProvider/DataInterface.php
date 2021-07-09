<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 12:59:09 AM
 */
interface CProvider_DataInterface {
    /**
     * @return array[] array2d
     */
    public function getData();
}

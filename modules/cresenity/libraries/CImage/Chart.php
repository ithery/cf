<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:17:11 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Chart {

    public static function createData() {
        return new CImage_Chart_Data();
    }

    public static function createImage() {
        return new CImage_Chart_Image();
    }

}

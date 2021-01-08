<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:17:11 AM
 */
class CImage_Chart {
    public static function createData() {
        return new CImage_Chart_Data();
    }

    public static function createImage($xSize, $ySize, CImage_Chart_Data $dataSet = null, $transparentBackground = false) {
        return new CImage_Chart_Image($xSize, $ySize, $dataSet, $transparentBackground);
    }
}

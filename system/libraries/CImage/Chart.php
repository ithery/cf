<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:17:11 AM
 */
class CImage_Chart {
    /**
     * @return CImage_Chart_Manager
     */
    public static function manager() {
        return CImage_Chart_Manager::instance();
    }

    public static function createData() {
        return new CImage_Chart_Data();
    }

    public static function createImage($xSize, $ySize, CImage_Chart_Data $dataSet = null, $transparentBackground = false) {
        return new CImage_Chart_Image($xSize, $ySize, $dataSet, $transparentBackground);
    }

    public static function createPie(CImage_Chart_Image $image, CImage_Chart_Data $data) {
        return new CImage_Chart_Pie($image, $data);
    }

    public static function createProcessor($options) {
        return new CImage_Chart_Processor($options);
    }

    public static function createBuilder($width = 500, $height = 500) {
        return new CImage_Chart_Builder($width, $height);
    }
}

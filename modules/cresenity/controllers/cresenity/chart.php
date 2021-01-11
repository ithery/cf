<?php

defined('SYSPATH') or die('No direct access allowed.');

use CImage_Chart_Constant as Constant;

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 30, 2019, 2:11:47 AM
 */
class Controller_Cresenity_Chart extends CController {
    use CTrait_Controller_Cresenity_Chart;

    public function __construct() {
    }

    public function json() {
        $json = CHTTP::request()->get('c', '');

        $data = json_decode($json, true);
        cdbg::dd($json, $data, json_last_error_msg());
    }

    public function __call($method, $args) {
        $chartType = CHTTP::request()->get('cht', '');
        $chartSize = CHTTP::request()->get('chs', '');
        $chartLabel = CHTTP::request()->get('chl', '');
        $chartData = CHTTP::request()->get('chd', '');

        $chartSizeArray = explode('x', $chartSize);
        $chartWidth = carr::get($chartSizeArray, 0, '250');
        $chartHeight = carr::get($chartSizeArray, 1, '250');
        if (!is_numeric($chartWidth)) {
            $chartWidth = '250';
        }
        if (!is_numeric($chartHeight)) {
            $chartHeight = '250';
        }
        $data = CImage_Chart::createData();

        $defaultDataLabel = null;
        if (cstr::startsWith($chartData, 't:')) {
            $chartData = substr($chartData, 2);
            $chartDataArray = explode('|', $chartData);
            foreach ($chartDataArray as $chda) {
                $seriesArray = explode(',', $chda);
                if ($defaultDataLabel === null) {
                    $defaultDataLabel = $seriesArray;
                }
                $data->addPoints($seriesArray, 'Score A');
            }
        }
        $data->setSerieDescription('Score A', 'Application A');

        $chartLabelArray = explode('|', $chartLabel);
        $haveLabel = is_array($chartLabelArray) && count($chartLabelArray) > 0 && strlen($chartLabelArray[0]) > 0;
        if (!$haveLabel) {
            $chartLabelArray = $defaultDataLabel;
        }
        $data->addPoints($chartLabelArray, 'Labels');
        $data->setAbscissa('Labels');
        /* Create the image */
        $image = CImage_Chart::createImage($chartWidth, $chartHeight, $data);

        $pie = CImage_Chart::createPie($image, $data);

        $centerX = $chartWidth / 2;
        $centerY = $chartHeight / 2;
        $radius = min($centerX, $centerY) - 10;

        $chartOptions = [
            'radius' => $radius,
            'drawLabels' => $haveLabel,
            'border' => true,
            'labelStacked' => true,
        ];

        if ($chartType == 'p3') {
            $pie->draw3DPie($centerX, $centerY, $chartOptions);
        }

        if ($chartType == 'p') {
            $pie->draw2DPie($centerX, $centerY, $chartOptions);
        }

        $image->autoOutput('chart.png');
        //echo $image->render('example.draw2DPie.png');
    }

    public function google() {
        $piChart = new CImage_GoogleChart_PieChart();
        // or if you installed via composer
        // $piChart = new gchart\gPieChart();

        $piChart->addDataSet([112, 315, 66, 40]);
        $piChart->setLabels(['first', 'second', 'third', 'fourth']);
        $piChart->setLegend(['first', 'second', 'third', 'fourth']);
        $piChart->setColors(['ff3344', '11ff11', '22aacc', '3333aa']);
        $piChart->renderImage();
    }
}

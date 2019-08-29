<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:12:43 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use CImage_Chart_Constant as Constant;

trait CTrait_Controller_Cresenity_Chart {

    public function line() {
        $data = CImage_Chart::createData();
        /* Create the X axis and the binded series */
        for ($i = 0; $i <= 360; $i = $i + 10) {
            $data->addPoints(cos(deg2rad($i)) * 20, "Probe 1");
        }
        for ($i = 0; $i <= 360; $i = $i + 10) {
            $data->addPoints(sin(deg2rad($i)) * 20, "Probe 2");
        }
        $data->setAxisName(0, "Index");
        $data->setAxisXY(0, Constant::AXIS_X);
        $data->setAxisPosition(0, Constant::AXIS_POSITION_BOTTOM);
        /* Create the Y axis and the binded series */
        for ($i = 0; $i <= 360; $i = $i + 10) {
            $data->addPoints($i, "Probe 3");
        }
        $data->setSerieOnAxis("Probe 3", 1);
        $data->setAxisName(1, "Degree");
        $data->setAxisXY(1, Constant::AXIS_Y);
        $data->setAxisUnit(1, "°");
        $data->setAxisPosition(1, Constant::AXIS_POSITION_RIGHT);
        /* Create the 1st scatter chart binding */
        $data->setScatterSerie("Probe 1", "Probe 3", 0);
        $data->setScatterSerieDescription(0, "This year");
        $data->setScatterSerieTicks(0, 4);
        $data->setScatterSerieColor(0, ["r" => 0, "g" => 0, "b" => 0]);

        /* Create the 2nd scatter chart binding */
        $data->setScatterSerie("Probe 2", "Probe 3", 1);
        $data->setScatterSerieDescription(1, "Last Year");

        /* Create the Image object */
        $image = new CImage_Chart_Image(400, 400, $data);

        /* Draw the background */
        $settings = ["r" => 170, "g" => 183, "b" => 87, "dash" => 1, "dashR" => 190, "dashG" => 203, "dashB" => 107];
        $image->drawFilledRectangle(0, 0, 400, 400, $settings);

        /* Overlay with a gradient */
        $settings = ["startR" => 219, "startG" => 231, "startB" => 139, "endR" => 1, "endG" => 138, "endB" => 68, "alpha" => 50];
        $image->drawGradientArea(0, 0, 400, 400, Constant::DIRECTION_VERTICAL, $settings);
        $image->drawGradientArea(0, 0, 400, 20, Constant::DIRECTION_VERTICAL, [
            "StartR" => 0,
            "StartG" => 0,
            "StartB" => 0,
            "EndR" => 50,
            "EndG" => 50,
            "EndB" => 50,
            "Alpha" => 80
        ]);

        /* Write the picture title */
        $image->setFontProperties(["fontName" => "Silkscreen.ttf", "fontSize" => 6]);
        $image->drawText(10, 13, "drawScatterLineChart() - Draw a scatter line chart", [
            "R" => 255,
            "G" => 255,
            "B" => 255
        ]);

        /* Add a border to the picture */
        $image->drawRectangle(0, 0, 399, 399, ["r" => 0, "g" => 0, "b" => 0]);

        /* Set the default font */
        $image->setFontProperties(["fontName" => "pf_arma_five.ttf", "fontSize" => 6]);

        /* Set the graph area */
        $image->setGraphArea(50, 50, 350, 350);

        /* Create the Scatter chart object */
        $myScatter = new Scatter($image, $data);

        /* Draw the scale */
        $myScatter->drawScatterScale();

        /* Turn on shadow computing */
        $image->setShadow(true, ["X" => 1, "Y" => 1, "R" => 0, "G" => 0, "B" => 0, "Alpha" => 10]);

        /* Draw a scatter plot chart */
        $myScatter->drawScatterLineChart();

        /* Draw the legend */
        $myScatter->drawScatterLegend(280, 380, ["Mode" => CImage_Chart::LEGEND_HORIZONTAL, "Style" => CImage_Chart::LEGEND_NOBORDER]);

        /* Render the picture (choose the best way) */
        $image->autoOutput("example.drawScatterLineChart.png");
    }

}

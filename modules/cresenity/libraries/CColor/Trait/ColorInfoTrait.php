<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 4:33:16 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CColor_Trait_ColorInfoTrait {

    static public $dictionary = array(
        'monochrome' => array(
            'bounds' => array(array(0, 0), array(100, 0)),
            'h' => NULL,
            's' => array(0, 100)
        ),
        'red' => array(
            'bounds' => array(array(20, 100), array(30, 92), array(40, 89), array(50, 85), array(60, 78), array(70, 70), array(80, 60), array(90, 55), array(100, 50)),
            'h' => array(-26, 18),
            's' => array(20, 100)
        ),
        'orange' => array(
            'bounds' => array(array(20, 100), array(30, 93), array(40, 88), array(50, 86), array(60, 85), array(70, 70), array(100, 70)),
            'h' => array(19, 46),
            's' => array(20, 100)
        ),
        'yellow' => array(
            'bounds' => array(array(25, 100), array(40, 94), array(50, 89), array(60, 86), array(70, 84), array(80, 82), array(90, 80), array(100, 75)),
            'h' => array(47, 62),
            's' => array(25, 100)
        ),
        'green' => array(
            'bounds' => array(array(30, 100), array(40, 90), array(50, 85), array(60, 81), array(70, 74), array(80, 64), array(90, 50), array(100, 40)),
            'h' => array(63, 178),
            's' => array(30, 100)
        ),
        'blue' => array(
            'bounds' => array(array(20, 100), array(30, 86), array(40, 80), array(50, 74), array(60, 60), array(70, 52), array(80, 44), array(90, 39), array(100, 35)),
            'h' => array(179, 257),
            's' => array(20, 100)
        ),
        'purple' => array(
            'bounds' => array(array(20, 100), array(30, 87), array(40, 79), array(50, 70), array(60, 65), array(70, 59), array(80, 52), array(90, 45), array(100, 42)),
            'h' => array(258, 282),
            's' => array(20, 100)
        ),
        'pink' => array(
            'bounds' => array(array(20, 100), array(30, 90), array(40, 86), array(60, 84), array(80, 80), array(90, 75), array(100, 73)),
            'h' => array(283, 334),
            's' => array(20, 100)
        )
    );

    private function getColorInfo($h) {
        // Maps red colors to make picking hue easier
        if ($h >= 334 && $h <= 360) {
            $h -= 360;
        }
        foreach (self::$dictionary as $color) {
            if ($color['h'] !== null && $h >= $color['h'][0] && $h <= $color['h'][1]) {
                return $color;
            }
        }
    }

}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 4:33:16 AM
 */
trait CColor_Trait_ColorInfoTrait {
    public static $dictionary = [
        'monochrome' => [
            'bounds' => [[0, 0], [100, 0]],
            'h' => null,
            's' => [0, 100]
        ],
        'red' => [
            'bounds' => [[20, 100], [30, 92], [40, 89], [50, 85], [60, 78], [70, 70], [80, 60], [90, 55], [100, 50]],
            'h' => [-26, 18],
            's' => [20, 100]
        ],
        'orange' => [
            'bounds' => [[20, 100], [30, 93], [40, 88], [50, 86], [60, 85], [70, 70], [100, 70]],
            'h' => [19, 46],
            's' => [20, 100]
        ],
        'yellow' => [
            'bounds' => [[25, 100], [40, 94], [50, 89], [60, 86], [70, 84], [80, 82], [90, 80], [100, 75]],
            'h' => [47, 62],
            's' => [25, 100]
        ],
        'green' => [
            'bounds' => [[30, 100], [40, 90], [50, 85], [60, 81], [70, 74], [80, 64], [90, 50], [100, 40]],
            'h' => [63, 178],
            's' => [30, 100]
        ],
        'blue' => [
            'bounds' => [[20, 100], [30, 86], [40, 80], [50, 74], [60, 60], [70, 52], [80, 44], [90, 39], [100, 35]],
            'h' => [179, 257],
            's' => [20, 100]
        ],
        'purple' => [
            'bounds' => [[20, 100], [30, 87], [40, 79], [50, 70], [60, 65], [70, 59], [80, 52], [90, 45], [100, 42]],
            'h' => [258, 282],
            's' => [20, 100]
        ],
        'pink' => [
            'bounds' => [[20, 100], [30, 90], [40, 86], [60, 84], [80, 80], [90, 75], [100, 73]],
            'h' => [283, 334],
            's' => [20, 100]
        ]
    ];

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

    private function getMinimumBrightness($h, $s) {
        $colorInfo = $this->getColorInfo($h);
        $bounds = $colorInfo['bounds'];
        for ($i = 0, $l = count($bounds); $i < $l - 1; $i++) {
            $s1 = $bounds[$i][0];
            $v1 = $bounds[$i][1];
            $s2 = $bounds[$i + 1][0];
            $v2 = $bounds[$i + 1][1];
            if ($s >= $s1 && $s <= $s2) {
                $m = ($v2 - $v1) / ($s2 - $s1);
                $b = $v1 - $m * $s1;

                return $m * $s + $b;
            }
        }

        return 0;
    }
}

<?php

trait CManager_Asset_SCSS_Trait_CompilerHelperTrait {
    protected function alterColor($args, $fn) {
        // helper function for adjust_color, change_color, and scale_color
        $color = $this->assertColor($args[0]);

        foreach ([1, 2, 3, 7] as $i) {
            if (isset($args[$i])) {
                $val = $this->assertNumber($args[$i]);
                $ii = $i == 7 ? 4 : $i; // alpha
                $color[$ii]
                    = $this->$fn(isset($color[$ii]) ? $color[$ii] : 0, $val, $i);
            }
        }

        if (isset($args[4]) || isset($args[5]) || isset($args[6])) {
            $hsl = $this->toHSL($color[1], $color[2], $color[3]);
            foreach ([4, 5, 6] as $i) {
                if (isset($args[$i])) {
                    $val = $this->assertNumber($args[$i]);
                    $hsl[$i - 3] = $this->$fn($hsl[$i - 3], $val, $i);
                }
            }

            $rgb = $this->toRGB($hsl[1], $hsl[2], $hsl[3]);
            if (isset($color[4])) {
                $rgb[4] = $color[4];
            }
            $color = $rgb;
        }

        return $color;
    }

    protected function adjustColorHelper($base, $alter, $i) {
        return $base += $alter;
    }

    protected function changeColorHelper($base, $alter, $i) {
        return $alter;
    }

    protected function scaleColorHelper($base, $scale, $i) {
        // 1,2,3 - rgb
        // 4, 5, 6 - hsl
        // 7 - a
        switch ($i) {
            case 1:
            case 2:
            case 3:
                $max = 255;

                break;
            case 4:
                $max = 360;

                break;
            case 7:
                $max = 1;

                break;
            default:
                $max = 100;
        }

        $scale = $scale / 100;
        if ($scale < 0) {
            return $base * $scale + $base;
        } else {
            return ($max - $base) * $scale + $base;
        }
    }
}

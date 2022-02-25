<?php

trait CManager_Asset_SCSS_Trait_CompilerLibTrait {
    protected function libIf($args) {
        list($cond, $t, $f) = $args;
        if (!$this->isTruthy($cond)) {
            return $f;
        }

        return $t;
    }

    protected function libIndex($args) {
        list($list, $value) = $args;
        $list = $this->assertList($list);

        $values = [];
        foreach ($list[2] as $item) {
            $values[] = $this->normalizeValue($item);
        }
        $key = array_search($this->normalizeValue($value), $values);

        return false === $key ? false : $key + 1;
    }

    protected function libRgb($args) {
        list($r, $g, $b) = $args;

        return ['color', $r[1], $g[1], $b[1]];
    }

    protected function libRgba($args) {
        if ($color = $this->coerceColor($args[0])) {
            $num = !isset($args[1]) ? $args[3] : $args[1];
            $alpha = $this->assertNumber($num);
            $color[4] = $alpha;

            return $color;
        }

        list($r, $g, $b, $a) = $args;

        return ['color', $r[1], $g[1], $b[1], $a[1]];
    }

    protected function libAdjustColor($args) {
        return $this->alterColor($args, 'adjustColorHelper');
    }

    protected function libChangeColor($args) {
        return $this->alterColor($args, 'changeColorHelper');
    }

    protected function libScaleColor($args) {
        return $this->alterColor($args, 'scaleColorHelper');
    }

    protected function libIeHexStr($args) {
        $color = $this->coerceColor($args[0]);
        $color[4] = isset($color[4]) ? round(255 * $color[4]) : 255;

        return sprintf('#%02X%02X%02X%02X', $color[4], $color[1], $color[2], $color[3]);
    }

    protected function libRed($args) {
        $color = $this->coerceColor($args[0]);

        return $color[1];
    }

    protected function libGreen($args) {
        $color = $this->coerceColor($args[0]);

        return $color[2];
    }

    protected function libBlue($args) {
        $color = $this->coerceColor($args[0]);

        return $color[3];
    }

    protected function libAlpha($args) {
        if ($color = $this->coerceColor($args[0])) {
            return isset($color[4]) ? $color[4] : 1;
        }

        // this might be the IE function, so return value unchanged
        return null;
    }

    protected function libOpacity($args) {
        $value = $args[0];
        if ($value[0] === 'number') {
            return null;
        }

        return $this->libAlpha($args);
    }

    protected function libMix($args) {
        list($first, $second, $weight) = $args;
        $first = $this->assertColor($first);
        $second = $this->assertColor($second);

        if (!isset($weight)) {
            $weight = 0.5;
        } else {
            $weight = $this->coercePercent($weight);
        }

        $firstAlpha = isset($first[4]) ? $first[4] : 1;
        $secondAlpha = isset($second[4]) ? $second[4] : 1;

        $w = $weight * 2 - 1;
        $a = $firstAlpha - $secondAlpha;

        $w1 = (($w * $a == -1 ? $w : ($w + $a) / (1 + $w * $a)) + 1) / 2.0;
        $w2 = 1.0 - $w1;

        $new = ['color',
            $w1 * $first[1] + $w2 * $second[1],
            $w1 * $first[2] + $w2 * $second[2],
            $w1 * $first[3] + $w2 * $second[3],
        ];

        if ($firstAlpha != 1.0 || $secondAlpha != 1.0) {
            $new[] = $firstAlpha * $weight + $secondAlpha * ($weight - 1);
        }

        return $this->fixColor($new);
    }

    protected function libHsl($args) {
        list($h, $s, $l) = $args;

        return $this->toRGB($h[1], $s[1], $l[1]);
    }

    protected function libHsla($args) {
        list($h, $s, $l, $a) = $args;
        $color = $this->toRGB($h[1], $s[1], $l[1]);
        $color[4] = $a[1];

        return $color;
    }

    protected function libHue($args) {
        $color = $this->assertColor($args[0]);
        $hsl = $this->toHSL($color[1], $color[2], $color[3]);

        return ['number', $hsl[1], 'deg'];
    }

    protected function libSaturation($args) {
        $color = $this->assertColor($args[0]);
        $hsl = $this->toHSL($color[1], $color[2], $color[3]);

        return ['number', $hsl[2], '%'];
    }

    protected function libLightness($args) {
        $color = $this->assertColor($args[0]);
        $hsl = $this->toHSL($color[1], $color[2], $color[3]);

        return ['number', $hsl[3], '%'];
    }

    protected function libAdjustHue($args) {
        $color = $this->assertColor($args[0]);
        $degrees = $this->assertNumber($args[1]);

        return $this->adjustHsl($color, 1, $degrees);
    }

    protected function libLighten($args) {
        $color = $this->assertColor($args[0]);
        $amount = 100 * $this->coercePercent($args[1]);

        return $this->adjustHsl($color, 3, $amount);
    }

    protected function libDarken($args) {
        $color = $this->assertColor($args[0]);
        $amount = 100 * $this->coercePercent($args[1]);

        return $this->adjustHsl($color, 3, -$amount);
    }

    protected function libSaturate($args) {
        $value = $args[0];
        if ($value[0] === 'number') {
            return null;
        }
        $color = $this->assertColor($value);
        $amount = 100 * $this->coercePercent($args[1]);

        return $this->adjustHsl($color, 2, $amount);
    }

    protected function libDesaturate($args) {
        $color = $this->assertColor($args[0]);
        $amount = 100 * $this->coercePercent($args[1]);

        return $this->adjustHsl($color, 2, -$amount);
    }

    protected function libGrayscale($args) {
        $value = $args[0];
        if ($value[0] === 'number') {
            return null;
        }

        return $this->adjustHsl($this->assertColor($value), 2, -100);
    }

    protected function libComplement($args) {
        return $this->adjustHsl($this->assertColor($args[0]), 1, 180);
    }

    protected function libInvert($args) {
        $value = $args[0];
        if ($value[0] === 'number') {
            return null;
        }
        $color = $this->assertColor($value);
        $color[1] = 255 - $color[1];
        $color[2] = 255 - $color[2];
        $color[3] = 255 - $color[3];

        return $color;
    }

    protected function libOpacify($args) {
        $color = $this->assertColor($args[0]);
        $amount = $this->coercePercent($args[1]);

        $color[4] = (isset($color[4]) ? $color[4] : 1) + $amount;
        $color[4] = min(1, max(0, $color[4]));

        return $color;
    }

    protected function libFadeIn($args) {
        return $this->lib_opacify($args);
    }

    protected function libTransparentize($args) {
        $color = $this->assertColor($args[0]);
        $amount = $this->coercePercent($args[1]);

        $color[4] = (isset($color[4]) ? $color[4] : 1) - $amount;
        $color[4] = min(1, max(0, $color[4]));

        return $color;
    }

    protected function libFadeOut($args) {
        return $this->libTransparentize($args);
    }

    protected function libUnquote($args) {
        $str = $args[0];
        if ($str[0] == 'string') {
            $str[1] = '';
        }

        return $str;
    }

    protected function libQuote($args) {
        $value = $args[0];
        if ($value[0] == 'string' && !empty($value[1])) {
            return $value;
        }

        return ['string', '"', [$value]];
    }

    protected function libPercentage($args) {
        return ['number',
            $this->coercePercent($args[0]) * 100,
            '%'];
    }

    protected function libRound($args) {
        $num = $args[0];
        $num[1] = round($num[1]);

        return $num;
    }

    protected function libFloor($args) {
        $num = $args[0];
        $num[1] = floor($num[1]);

        return $num;
    }

    protected function libCeil($args) {
        $num = $args[0];
        $num[1] = ceil($num[1]);

        return $num;
    }

    protected function libAbs($args) {
        $num = $args[0];
        $num[1] = abs($num[1]);

        return $num;
    }

    protected function libMin($args) {
        $numbers = $this->getNormalizedNumbers($args);
        $min = null;
        foreach ($numbers as $key => $number) {
            if (null === $min || $number[1] <= $min[1]) {
                $min = [$key, $number[1]];
            }
        }

        return $args[$min[0]];
    }

    protected function libMax($args) {
        $numbers = $this->getNormalizedNumbers($args);
        $max = null;
        foreach ($numbers as $key => $number) {
            if (null === $max || $number[1] >= $max[1]) {
                $max = [$key, $number[1]];
            }
        }

        return $args[$max[0]];
    }

    protected function libLength($args) {
        $list = $this->coerceList($args[0]);

        return count($list[2]);
    }

    protected function libNth($args) {
        $list = $this->coerceList($args[0]);
        $n = $this->assertNumber($args[1]) - 1;

        return isset($list[2][$n]) ? $list[2][$n] : CManager_Asset_SCSS_Compiler::$defaultValue;
    }

    protected function libJoin($args) {
        list($list1, $list2, $sep) = $args;
        $list1 = $this->coerceList($list1, ' ');
        $list2 = $this->coerceList($list2, ' ');
        $sep = $this->listSeparatorForJoin($list1, $sep);

        return ['list', $sep, array_merge($list1[2], $list2[2])];
    }

    protected function libAppend($args) {
        list($list1, $value, $sep) = $args;
        $list1 = $this->coerceList($list1, ' ');
        $sep = $this->listSeparatorForJoin($list1, $sep);

        return ['list', $sep, array_merge($list1[2], [$value])];
    }

    protected function libZip($args) {
        foreach ($args as $arg) {
            $this->assertList($arg);
        }

        $lists = [];
        $firstList = array_shift($args);
        foreach ($firstList[2] as $key => $item) {
            $list = ['list', '', [$item]];
            foreach ($args as $arg) {
                if (isset($arg[2][$key])) {
                    $list[2][] = $arg[2][$key];
                } else {
                    break 2;
                }
            }
            $lists[] = $list;
        }

        return ['list', ',', $lists];
    }

    protected function libTypeOf($args) {
        $value = $args[0];
        switch ($value[0]) {
            case 'keyword':
                if ($value == CManager_Asset_SCSS_Compiler::$true || $value == CManager_Asset_SCSS_Compiler::$false) {
                    return 'bool';
                }

                if ($this->coerceColor($value)) {
                    return 'color';
                }

                return 'string';
            default:
                return $value[0];
        }
    }

    protected function libUnit($args) {
        $num = $args[0];
        if ($num[0] == 'number') {
            return ['string', '"', [$num[2]]];
        }

        return '';
    }

    protected function libUnitless($args) {
        $value = $args[0];

        return $value[0] == 'number' && empty($value[2]);
    }

    protected function libComparable($args) {
        list($number1, $number2) = $args;
        if (!isset($number1[0]) || $number1[0] != 'number' || !isset($number2[0]) || $number2[0] != 'number') {
            $this->throwError('Invalid argument(s) for "comparable"');
        }

        $number1 = $this->normalizeNumber($number1);
        $number2 = $this->normalizeNumber($number2);

        return $number1[2] == $number2[2] || $number1[2] == '' || $number2[2] == '';
    }

    /**
     * Workaround IE7's content counter bug.
     *
     * @param array $args
     */
    protected function libCounter($args) {
        $list = array_map([$this, 'compileValue'], $args);

        return ['string', '', ['counter(' . implode(',', $list) . ')']];
    }
}

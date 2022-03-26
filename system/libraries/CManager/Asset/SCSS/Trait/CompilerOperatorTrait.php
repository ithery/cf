<?php

trait CManager_Asset_SCSS_Trait_CompilerOperatorTrait {

    protected function opAddNumberNumber($left, $right) {
        return ['number', $left[1] + $right[1], $left[2]];
    }

    protected function opMulNumberNumber($left, $right) {
        return ['number', $left[1] * $right[1], $left[2]];
    }

    protected function opSubNumberNumber($left, $right) {
        return ['number', $left[1] - $right[1], $left[2]];
    }

    protected function opDivNumberNumber($left, $right) {
        return ['number', $left[1] / $right[1], $left[2]];
    }

    protected function opModNumberNumber($left, $right) {
        return ['number', $left[1] % $right[1], $left[2]];
    }

    // adding strings
    protected function opAdd($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        if ($strLeft = $this->coerceString($left)) {
            if ($right[0] == 'string') {
                $right[1] = '';
            }
            $strLeft[2][] = $right;

            return $strLeft;
        }

        if ($strRight = $this->coerceString($right)) {
            if ($left[0] == 'string') {
                $left[1] = '';
            }
            array_unshift($strRight[2], $left);

            return $strRight;
        }
    }

    protected function opAnd($left, $right, $shouldEval) {
        /** @var CManager_Asset_SCSS_Compiler $this */

        if (!$shouldEval) {
            return;
        }
        if ($left != CManager_Asset_SCSS_Compiler::$false) {
            return $right;
        }

        return $left;
    }

    protected function opOr($left, $right, $shouldEval) {
        /** @var CManager_Asset_SCSS_Compiler $this */

        if (!$shouldEval) {
            return;
        }
        if ($left != CManager_Asset_SCSS_Compiler::$false) {
            return $left;
        }

        return $right;
    }

    protected function opColorColor($op, $left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        $out = ['color'];
        foreach (range(1, 3) as $i) {
            $lval = isset($left[$i]) ? $left[$i] : 0;
            $rval = isset($right[$i]) ? $right[$i] : 0;
            switch ($op) {
                case '+':
                    $out[] = $lval + $rval;

                    break;
                case '-':
                    $out[] = $lval - $rval;

                    break;
                case '*':
                    $out[] = $lval * $rval;

                    break;
                case '%':
                    $out[] = $lval % $rval;

                    break;
                case '/':
                    if ($rval == 0) {
                        $this->throwError("color: Can't divide by zero");
                    }
                    $out[] = $lval / $rval;

                    break;
                case '==':
                    return $this->opEq($left, $right);
                case '!=':
                    return $this->opNeq($left, $right);
                default:
                    $this->throwError("color: unknown op ${op}");
            }
        }

        if (isset($left[4])) {
            $out[4] = $left[4];
        } elseif (isset($right[4])) {
            $out[4] = $right[4];
        }

        return $this->fixColor($out);
    }

    protected function op_color_number($op, $left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        $value = $right[1];

        return $this->opColorColor(
            $op,
            $left,
            ['color', $value, $value, $value]
        );
    }

    protected function opNumberColor($op, $left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        $value = $left[1];

        return $this->opColorColor(
            $op,
            ['color', $value, $value, $value],
            $right
        );
    }

    protected function opEq($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        if (($lStr = $this->coerceString($left)) && ($rStr = $this->coerceString($right))) {
            $lStr[1] = '';
            $rStr[1] = '';

            return $this->toBool($this->compileValue($lStr) == $this->compileValue($rStr));
        }

        return $this->toBool($left == $right);
    }

    protected function opNeq($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        return $this->toBool($left != $right);
    }

    protected function opGteNumberNumber($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        return $this->toBool($left[1] >= $right[1]);
    }

    protected function opGtNumberNumber($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        return $this->toBool($left[1] > $right[1]);
    }

    protected function opLteNumberNumber($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        return $this->toBool($left[1] <= $right[1]);
    }

    protected function opLtNumberNumber($left, $right) {
        /** @var CManager_Asset_SCSS_Compiler $this */
        return $this->toBool($left[1] < $right[1]);
    }

}

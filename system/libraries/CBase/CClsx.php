<?php
/**
 * Based on https://github.com/lukeed/clsx.
 */
class CBase_CClsx {
    private static function toVal($mix) {
        $str = '';
        if (is_string($mix) || is_numeric($mix)) {
            $str .= $mix;
        } elseif (is_array($mix)) {
            if (carr::isAssoc($mix)) {
                foreach ($mix as $k => $y) {
                    if ($y) {
                        $str && ($str .= ' ');
                        $str .= $k;
                    }
                }
            } else {
                foreach ($mix as $y) {
                    if ($y) {
                        if ($y = self::toVal($y)) {
                            $str && ($str .= ' ');
                            $str .= $y;
                        }
                    }
                }
            }
        }

        return $str;
    }

    public static function clsx() {
        $args = func_get_args();
        $str = '';
        foreach ($args as $arg) {
            if ($x = self::toVal($arg)) {
                $str && $str .= ' ';
                $str .= $x;
            }
        }

        return $str;
    }
}

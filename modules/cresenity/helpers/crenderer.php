<?php

//@codingStandardsIgnoreStart
class crenderer {
    /**
     * Render as css styles from array key value
     *
     * @param array $styles
     *
     * @return string
     *
     * @deprecated 1.1
     */
    public static function render_style($styles) {
        $ret = '';
        foreach ($styles as $k => $v) {
            $ret .= $k . ':' . $v . ';';
        }
        return $ret;
    }
}

//@codingStandardsIgnoreEnd

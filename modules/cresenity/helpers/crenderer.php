<?php

//@codingStandardsIgnoreStart
class crenderer {
    /**
     * Undocumented function
     *
     * @param [type] $styles
     *
     * @return void
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

<?php

class crenderer {

    public static function render_style($styles) {
        $ret = "";
        foreach ($styles as $k => $v) {
            $ret .=$k . ":" . $v . ";";
        }
        return $ret;
    }

}

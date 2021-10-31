<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated since 1.2
 */
class crenderer {
    /**
     * Render as css styles from array key value
     *
     * @param array $styles
     *
     * @return string
     *
     * @see CRenderable
     * @deprecated 1.1, use CRenderable::renderStyles
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

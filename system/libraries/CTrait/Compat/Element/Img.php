<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @see CElement_Element_Img
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Img {
    /**
     * Set Attribute src.
     *
     * @param string $src
     *
     * @return $this
     * @deprecated 1.2
     */
    public function set_src($src) {
        /** @var CElement_Element_Img $this */
        return $this->setSrc($src);
    }
}

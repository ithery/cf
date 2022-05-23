<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 24, 2019, 12:39:06 PM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Element_Img {
    /**
     * Set Attribute src.
     *
     * @param string $src
     *
     * @return @deprecated 1.2
     */
    public function set_src($src) {
        return $this->setSrc($src);
    }
}

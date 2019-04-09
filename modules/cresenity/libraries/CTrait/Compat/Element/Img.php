<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 24, 2019, 12:39:06 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Element_Img {

    public function set_src($src) {
        return $this->setSrc($src);
    }

}

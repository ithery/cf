<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 19, 2019, 5:31:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Resources_Engine_Image {

    /**
     * @deprecated since version 1.2
     * @param string $size_name
     * @param array $options
     * @return $this
     */
    public function add_size($size_name, $options) {
        return $this->addSize($size_name, $options);
    }

}

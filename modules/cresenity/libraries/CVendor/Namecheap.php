<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Oct 25, 2018, 5:14:37 PM
 */
class CVendor_Namecheap {
    /**
     * @param type $options
     *
     * @return \CVendor_Namecheap_Api
     */
    public static function api($options) {
        return new CVendor_Namecheap_Api($options);
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 3:34:53 PM
 */
class CCookie {
    public static function jar() {
        return new CCookie_Jar();
    }
}

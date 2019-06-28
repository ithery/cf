<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 3:34:53 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CCookie {

    public static function jar() {
        return new CCookie_Jar();
    }

}

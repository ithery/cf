<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * 
 * @author Hery Kurniawan
 * @since Jun 6, 2018, 11:32:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax {

    public static function createMethod($json = null) {
        if ($json != null) {
            return CAjax_Method::createFromJson($json);
        }
        return new CAjax_Method();
    }

}

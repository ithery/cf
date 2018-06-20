<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * 
 * @author Hery Kurniawan
 * @since Jun 6, 2018, 11:32:00 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax {

    public static function createMethod($options = null) {
        if (!is_array($options)) {
            if ($options != null) {
                return CAjax_Method::createFromJson($options);
            }
        }
        return new CAjax_Method($options);
    }

}

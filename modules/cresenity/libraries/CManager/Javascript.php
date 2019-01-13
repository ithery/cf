<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 12:38:20 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_Javascript {

    public static function jQuery() {
        return CJavascript::jqueryStatement();
    }

    public static function raw($js) {
        return CJavascript::rawStatement($js);
    }
    
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2019, 1:39:19 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CString {

    public static function initials($name = null) {
        return new CString_Initials($name);
    }

    public static function language() {
        return new CString_Language();
    }

}

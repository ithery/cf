<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 4:18:08 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDetector {

    /**
     * 
     * @return \CDetector_Mobile
     */
    public static function mobile() {
        return new CDetector_Mobile();
    }

}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 23, 2019, 4:18:08 AM
 */
class CDetector {
    /**
     * @return \CDetector_Mobile
     */
    public static function mobile() {
        return new CDetector_Mobile();
    }
}

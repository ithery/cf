<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 24, 2019, 1:21:39 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAnalytics {
    public static function createPeriod() {
        return new CAnalytics_Period();
    }
}

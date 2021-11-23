<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Apr 16, 2019, 1:20:03 AM
 */
class CDaemon_Loop {
    /**
     * @return \CDaemon_Loop_ReactFactory
     */
    public static function reactFactory() {
        return new CDaemon_Loop_ReactFactory();
    }
}

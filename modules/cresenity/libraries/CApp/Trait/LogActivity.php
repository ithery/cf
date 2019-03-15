<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 2:20:52 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_LogActivity {

    public function start($message, $listener = null) {
        CApp_LogActivity::instance()->start($message, $listener);
    }

    public function stop() {
        CApp_LogActivity::instance()->stop();
    }

}

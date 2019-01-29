<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 12:16:41 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CEvent {

    public static function createDispatcher() {
        return new CEvent_Dispatcher(new CContainer());
    }

}

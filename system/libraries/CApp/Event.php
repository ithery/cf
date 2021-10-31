<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 1:22:05 PM
 */
class CApp_Event {
    public static function createEventOnRenderableAdded($renderable) {
        return new CApp_Event_OnRenderableAdded($renderable);
    }
}

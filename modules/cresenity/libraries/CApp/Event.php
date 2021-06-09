<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 1, 2018, 1:22:05 PM
 */
class CApp_Event {
    const ON_RENDERABLE_ADDED = 'CApp_Event_OnRenderableAdded';

    public static function createOnRenderableAddedListener($renderable) {
        $className = self::ON_RENDERABLE_ADDED;
        return new $className($renderable);
    }
}

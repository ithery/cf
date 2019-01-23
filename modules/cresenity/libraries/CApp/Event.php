<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 1, 2018, 1:22:05 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Event extends CEvent_Dispatcher {

    const onRenderableAdded = 'CApp_Event_OnRenderableAdded';

    public static function createOnRenderableAddedListener($renderable) {
        $className = self::onRenderableAdded;
        return new $className($renderable);
    }

}

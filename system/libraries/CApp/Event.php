<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Event {
    public static function createEventOnRenderableAdded($renderable) {
        return new CApp_Event_OnRenderableAdded($renderable);
    }
}

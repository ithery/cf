<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_HydrationMiddleware_RenderView implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($unHydratedInstance, $request) {
    }

    public static function dehydrate($instance, $response) {
        $html = $instance->output();

        c::set($response, 'effects.html', $html);
    }
}

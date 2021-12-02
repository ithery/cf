<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_HydrationMiddleware_RenderView implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($unHydratedInstance, $request) {
        //
    }

    public static function dehydrate($instance, $response) {
        $html = $instance->output();

        c::set($response, 'effects.html', $html);
    }
}

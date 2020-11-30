<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_HydrationMiddleware_RenderView implements CComponent_HydrationMiddlewareInterface {

    public static function hydrate($unHydratedInstance, $request) {
        //
    }

    public static function dehydrate($instance, $response) {
        $html = $instance->output();

        CF::set($response, 'effects.html', $html);
    }

}

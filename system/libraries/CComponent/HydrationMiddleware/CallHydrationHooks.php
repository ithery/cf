<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_HydrationMiddleware_CallHydrationHooks implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($instance, $request) {
        CComponent_Manager::instance()->dispatch('component.hydrate', $instance, $request);
        CComponent_Manager::instance()->dispatch('component.hydrate.subsequent', $instance, $request);

        $instance->hydrate($request);
    }

    public static function dehydrate($instance, $response) {
        $instance->dehydrate($response);

        CComponent_Manager::instance()->dispatch('component.dehydrate', $instance, $response);
        CComponent_Manager::instance()->dispatch('component.dehydrate.subsequent', $instance, $response);
    }

    public static function initialDehydrate($instance, $response) {
        $instance->dehydrate($response);

        CComponent_Manager::instance()->dispatch('component.dehydrate', $instance, $response);
        CComponent_Manager::instance()->dispatch('component.dehydrate.initial', $instance, $response);
    }

    public static function initialHydrate($instance, $request) {
        CComponent_Manager::instance()->dispatch('component.hydrate', $instance, $request);
        CComponent_Manager::instance()->dispatch('component.hydrate.initial', $instance, $request);
    }
}

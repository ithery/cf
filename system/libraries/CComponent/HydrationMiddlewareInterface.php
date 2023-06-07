<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CComponent_HydrationMiddlewareInterface {
    public static function hydrate($instance, $request);

    public static function dehydrate($instance, $response);
}

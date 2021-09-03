<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
interface CComponent_HydrationMiddlewareInterface {

    public static function hydrate($instance, $request);

    public static function dehydrate($instance, $response);
}

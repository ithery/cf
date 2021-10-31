<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
interface CComponent_HydrationMiddlewareInterface {
    public static function hydrate($instance, $request);

    public static function dehydrate($instance, $response);
}

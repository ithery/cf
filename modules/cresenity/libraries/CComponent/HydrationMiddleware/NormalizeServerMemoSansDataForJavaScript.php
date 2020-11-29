<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */

class CComponent_HydrationMiddleware_NormalizeServerMemoSansDataForJavaScript extends CComponent_HydrationMiddleware_NormalizeDataForJavaScript implements CComponent_HydrationMiddlewareInterface {

    public static function hydrate($instance, $request)
    {
        //
    }

    public static function dehydrate($instance, $response)
    {
        foreach ($response->memo as $key => $value) {
            if ($key === 'data') continue;

             if (is_array($value)) {
                $response->memo[$key] = static::reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
            }
        }
    }
}

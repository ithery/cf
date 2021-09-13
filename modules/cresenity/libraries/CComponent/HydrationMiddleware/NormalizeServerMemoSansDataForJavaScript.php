<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_HydrationMiddleware_NormalizeServerMemoSansDataForJavaScript extends CComponent_HydrationMiddleware_NormalizeDataForJavaScript implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($instance, $request) {
        //
    }

    public static function dehydrate($instance, $response) {
        foreach ($response->memo as $key => $value) {
            if ($key === 'data') {
                continue;
            }

            if (is_array($value)) {
                $response->memo[$key] = static::reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
            }
        }
    }
}

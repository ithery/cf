<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 29, 2020 
 * @license Ittron Global Teknologi
 */
class CComponent_HydrationMiddleware_NormalizeComponentPropertiesForJavaScript extends CComponent_HydrationMiddleware_NormalizeDataForJavaScript implements CComponent_HydrationMiddlewareInterface {

    public static function hydrate($instance, $request)
    {
        //
    }

    public static function dehydrate($instance, $response)
    {
        foreach ($instance->getPublicPropertiesDefinedBySubClass() as $key => $value) {
            if (is_array($value)) {
                $instance->$key = static::reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
            }

            if ($value instanceof EloquentCollection) {
                // Preserve collection items order by reindexing underlying array.
                $instance->$key = $value->values();
            }
        }
    }

}

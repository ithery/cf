<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_HydrationMiddleware_NormalizeComponentPropertiesForJavaScript extends CComponent_HydrationMiddleware_NormalizeDataForJavaScript implements CComponent_HydrationMiddlewareInterface {
    public static function hydrate($instance, $request) {
    }

    public static function dehydrate($instance, $response) {
        foreach ($instance->getPublicPropertiesDefinedBySubClass() as $key => $value) {
            if (is_array($value)) {
                $instance->$key = static::reindexArrayWithNumericKeysOtherwiseJavaScriptWillMessWithTheOrder($value);
            }

            if ($value instanceof CModel_Collection) {
                // Preserve collection items order by reindexing underlying array.
                $instance->$key = $value->values();
            }
        }
    }
}

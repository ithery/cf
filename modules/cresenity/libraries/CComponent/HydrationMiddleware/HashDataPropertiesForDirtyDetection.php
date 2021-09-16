<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 29, 2020
 */
class CComponent_HydrationMiddleware_HashDataPropertiesForDirtyDetection implements CComponent_HydrationMiddlewareInterface {
    protected static $propertyHashesByComponentId = [];

    public static function hydrate($instance, $request) {
        $data = c::get($request, 'memo.data', []);

        c::collect($data)->each(function ($value, $key) use ($instance) {
            if (is_array($value)) {
                foreach (carr::dot($value, $key . '.') as $dottedKey => $value) {
                    static::rehashProperty($dottedKey, $value, $instance);
                }
            } else {
                static::rehashProperty($key, $value, $instance);
            }
        });
    }

    public static function dehydrate($instance, $response) {
        $data = c::get($response, 'memo.data', []);

        $dirtyProps = c::collect(isset(static::$propertyHashesByComponentId[$instance->id]) ? static::$propertyHashesByComponentId[$instance->id] : [])
                ->filter(function ($hash, $key) use ($data) {
                    // Only return the propertyHashes/props that have changed.
                    return static::hash(c::get($data, $key)) !== $hash;
                })
                ->keys()
                ->toArray();

        c::set($response, 'effects.dirty', $dirtyProps);
    }

    public static function rehashProperty($name, $value, $component) {
        static::$propertyHashesByComponentId[$component->id][$name] = static::hash($value);
    }

    public static function hash($value) {
        if (!is_null($value) && !is_string($value) && !is_numeric($value) && !is_bool($value)) {
            if (is_array($value)) {
                return json_encode($value);
            }
            $value = method_exists($value, '__toString') ? (string) $value : json_encode($value);
        }

        // Using crc32 because it's fast, and this doesn't have to be secure.
        return crc32($value);
    }
}

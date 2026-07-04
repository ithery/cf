<?php

use Illuminate\Contracts\Support\Arrayable;

/**
 * Static helper methods shared across CElement classes.
 */
class CElement_Helper {
    /**
     * Normalize a class list (space-separated string, array, Arrayable or CCollection)
     * into a plain array of non-blank class names.
     *
     * @param array|string|Arrayable|CCollection $classes
     *
     * @return array
     */
    public static function getClasses($classes) {
        if (is_string($classes)) {
            return c::collect(explode(' ', $classes))->filter(function ($class) {
                return !c::blank($class);
            })->all();
        }
        if ($classes instanceof CCollection) {
            return $classes->filter(function ($class) {
                return !c::blank($class);
            })->all();
        }
        if ($classes instanceof Arrayable) {
            return $classes->toArray();
        }
        if (is_array($classes)) {
            return $classes;
        }

        return [];
    }
}

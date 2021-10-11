<?php

class CElement_Helper {
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
        if ($classes instanceof CInterface_Arrayable) {
            return $classes->toArray();
        }
        if (is_array($classes)) {
            return $classes;
        }
        return [];
    }
}

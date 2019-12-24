<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Common helper class.
 */

class c {

    public static function manimgurl($path) {
        return curl::base() . "public/manual/" . $path;
    }

    public static function baseIteratee($value) {
        if (\is_callable($value)) {
            return $value;
        }
        if (null === $value) {
            return '_\identity';
        }
        if (\is_array($value)) {
            return 2 === \count($value) && [0, 1] === \array_keys($value) ? static::baseMatchesProperty($value[0], $value[1]) : baseMatches($value);
        }
        return property($value);
    }

    public static function baseMatchesProperty($property, $source) {
        return function ($value, $index, $collection) use ($property, $source) {
            $propertyVal = static::property($property);
            return static::isEqual($propertyVal($value, $index, $collection), $source);
        };
    }

    public static function isEqual($value, $other) {
        $factory = CComparator::createFactory();
        $comparator = $factory->getComparatorFor($value, $other);
        try {
            $comparator->assertEquals($value, $other);
            return true;
        } catch (CComparator_Exception_ComparisonFailureException $failure) {
            return false;
        }
    }

}

// End c
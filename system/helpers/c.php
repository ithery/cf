<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Common helper class.
 */
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class c {

    public static function urShift($a, $b) {
        if ($b == 0) {
            return $a;
        }
        return ($a >> $b) & ~(1 << (8 * PHP_INT_SIZE - 1) >> ($b - 1));
    }

    public static function manimgurl($path) {
        return curl::base() . "public/manual/" . $path;
    }

    public static function baseIteratee($value) {
        if (\is_callable($value)) {
            return $value;
        }
        if (null === $value) {
            return array('c', 'identity');
        }
        if (\is_array($value)) {
            return 2 === \count($value) && [0, 1] === \array_keys($value) ? static::baseMatchesProperty($value[0], $value[1]) : static::baseMatches($value);
        }
        return property($value);
    }

    public static function baseMatchesProperty($property, $source) {
        return function ($value, $index, $collection) use ($property, $source) {
            $propertyVal = static::property($property);
            return static::isEqual($propertyVal($value, $index, $collection), $source);
        };
    }

    function baseMatches($source) {
        return function ($value, $index, $collection) use ($source) {
            if ($value === $source || isEqual($value, $source)) {
                return true;
            }
            if (\is_array($source) || $source instanceof \Traversable) {
                foreach ($source as $k => $v) {
                    if (!static::isEqual(property($k)($value, $index, $collection), $v)) {
                        return false;
                    }
                }
                return true;
            }
            return false;
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

    /**
     * Creates a function that returns the value at `path` of a given object.
     *
     * @param array|string $path The path of the property to get.
     *
     * @return callable Returns the new accessor function.
     * @example
     * <code>
     * $objects = [
     *   [ 'a' => [ 'b' => 2 ] ],
     *   [ 'a' => [ 'b' => 1 ] ]
     * ];
     *
     * carr::map($objects, property('a.b'));
     * // => [2, 1]
     *
     * carr::map(sortBy($objects, property(['a', 'b'])), 'a.b');
     * // => [1, 2]
     * </code>
     */
    public static function property($path) {
        $propertyAccess = PropertyAccess::createPropertyAccessorBuilder()
                ->disableExceptionOnInvalidIndex()
                ->getPropertyAccessor();
        return function ($value, $index = 0, $collection = []) use ($path, $propertyAccess) {
            $path = \implode('.', (array) $path);
            if (\is_array($value)) {
                if (false !== \strpos($path, '.')) {
                    $paths = \explode('.', $path);
                    foreach ($paths as $path) {
                        $value = property($path)($value, $index, $collection);
                    }
                    return $value;
                }
                if (\is_string($path) && $path[0] !== '[' && $path[-1] !== ']') {
                    $path = "[$path]";
                }
            }
            try {
                return $propertyAccess->getValue($value, $path);
            } catch (NoSuchPropertyException $e) {
                return null;
            } catch (NoSuchIndexException $e) {
                return null;
            }
        };
    }

}

// End c
<?php

/**
 * Compares Exception instances for equality.
 */
class CComparator_Engine_ExceptionComparator extends CComparator_Engine_ObjectComparator {
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return $expected instanceof \Exception && $actual instanceof \Exception;
    }

    /**
     * Converts an object to an array containing all of its private, protected
     * and public properties.
     *
     * @param object $object
     *
     * @return array
     */
    protected function toArray($object) {
        $array = parent::toArray($object);
        unset(
            $array['file'],
            $array['line'],
            $array['trace'],
            $array['string'],
            $array['xdebug_message']
        );

        return $array;
    }
}

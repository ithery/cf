<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Compares PHPUnit_Framework_MockObject_MockObject instances for equality.
 */
class CComparator_Engine_MockObjectComparator extends CComparator_Engine_ObjectComparator {

    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual) {
        return ($expected instanceof \PHPUnit_Framework_MockObject_MockObject || $expected instanceof \PHPUnit\Framework\MockObject\MockObject) &&
                ($actual instanceof \PHPUnit_Framework_MockObject_MockObject || $actual instanceof \PHPUnit\Framework\MockObject\MockObject);
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
        unset($array['__phpunit_invocationMocker']);
        return $array;
    }

}

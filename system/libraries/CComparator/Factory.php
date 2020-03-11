<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Factory for comparators which compare values for equality.
 */
class CComparator_Factory {

    /**
     * @var Factory
     */
    private static $instance;

    /**
     * @var Comparator[]
     */
    private $customComparators = [];

    /**
     * @var Comparator[]
     */
    private $defaultComparators = [];

    /**
     * @return Factory
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self; // @codeCoverageIgnore
        }
        return self::$instance;
    }

    /**
     * Constructs a new factory.
     */
    public function __construct() {
        $this->registerDefaultComparators();
    }

    /**
     * Returns the correct comparator for comparing two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return Comparator
     */
    public function getComparatorFor($expected, $actual) {
        foreach ($this->customComparators as $comparator) {
            if ($comparator->accepts($expected, $actual)) {
                return $comparator;
            }
        }
        foreach ($this->defaultComparators as $comparator) {
            if ($comparator->accepts($expected, $actual)) {
                return $comparator;
            }
        }
    }

    /**
     * Registers a new comparator.
     *
     * This comparator will be returned by getComparatorFor() if its accept() method
     * returns TRUE for the compared values. It has higher priority than the
     * existing comparators, meaning that its accept() method will be invoked
     * before those of the other comparators.
     *
     * @param Comparator $comparator The comparator to be registered
     */
    public function register(CComparator_AbstractEngine $comparator) {
        \array_unshift($this->customComparators, $comparator);
        $comparator->setFactory($this);
    }

    /**
     * Unregisters a comparator.
     *
     * This comparator will no longer be considered by getComparatorFor().
     *
     * @param Comparator $comparator The comparator to be unregistered
     */
    public function unregister(CComparator_AbstractEngine $comparator) {
        foreach ($this->customComparators as $key => $_comparator) {
            if ($comparator === $_comparator) {
                unset($this->customComparators[$key]);
            }
        }
    }

    /**
     * Unregisters all non-default comparators.
     */
    public function reset() {
        $this->customComparators = [];
    }

    private function registerDefaultComparators() {
        $this->registerDefaultComparator(new CComparator_Engine_MockObjectComparator);
        $this->registerDefaultComparator(new CComparator_Engine_DateTimeComparator);
        $this->registerDefaultComparator(new CComparator_Engine_DOMNodeComparator);
        $this->registerDefaultComparator(new CComparator_Engine_SplObjectStorageComparator);
        $this->registerDefaultComparator(new CComparator_Engine_ExceptionComparator);
        $this->registerDefaultComparator(new CComparator_Engine_ObjectComparator);
        $this->registerDefaultComparator(new CComparator_Engine_ResourceComparator);
        $this->registerDefaultComparator(new CComparator_Engine_ArrayComparator);
        $this->registerDefaultComparator(new CComparator_Engine_DoubleComparator);
        $this->registerDefaultComparator(new CComparator_Engine_NumericComparator);
        $this->registerDefaultComparator(new CComparator_Engine_ScalarComparator);
        $this->registerDefaultComparator(new CComparator_Engine_TypeComparator);
    }

    private function registerDefaultComparator(CComparator_AbstractEngine $comparator) {
        $this->defaultComparators[] = $comparator;
        $comparator->setFactory($this);
    }

}

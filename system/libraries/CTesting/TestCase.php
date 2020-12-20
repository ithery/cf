<?php

use PHPUnit\Framework\TestCase as BaseTestCase;

class CTesting_TestCase extends BaseTestCase {
    use CTesting_Concern_MakesHttpRequests;
    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    protected function createApplication() {
    }
}

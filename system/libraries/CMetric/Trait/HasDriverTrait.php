<?php

trait CMetric_Trait_HasDriverTrait {
    /**
     * @var CMetric_DriverAbstract
     */
    protected $driver;

    /**
     * Return our own driver if we have one, otherwise our Metrics default driver.
     *
     * @return CMetric_DriverAbstract
     */
    public function getDriver() {
        return $this->driver
            ? $this->driver
            : CMetric_Manager::instance()->driver();
    }

    /**
     * @param CMetric_DriverAbstract $driver
     *
     * @return $this
     */
    public function setDriver(CMetric_DriverAbstract $driver) {
        $this->driver = $driver;

        return $this;
    }
}

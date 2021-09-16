<?php

abstract class CEmail_DriverAbstract implements CEmail_DriverInterface {
    /**
     * Email Config
     *
     * @var CEmail_Config
     */
    protected $config;

    /**
     * @param CEmail_Config $config
     */
    public function __construct(CEmail_Config $config) {
        $this->config = $config;
    }

    /**
     * Get Email Config
     *
     * @return CEmail_Config
     */
    public function getConfig() {
        return $this->config;
    }
}

<?php

// namespace League\Flysystem;

/**
 * @internal
 */
trait League_Flysystem_ConfigAwareTrait
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * Set the config.
     *
     * @param Config|array|null $config
     */
    protected function setConfig($config)
    {
        $this->config = $config ? League_Flysystem_Util::ensureConfig($config) : new League_Flysystem_Config;
    }

    /**
     * Get the Config.
     *
     * @return Config config object
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Convert a config array to a Config object with the correct fallback.
     *
     * @param array $config
     *
     * @return Config
     */
    protected function prepareConfig(array $config)
    {
        $config = new League_Flysystem_Config($config);
        $config->setFallback($this->getConfig());

        return $config;
    }
}

<?php

/**
 * Description of ServiceManager.
 */
abstract class CDevSuite_ServiceManager {
    /**
     * Start the given services.
     *
     * @param string|array $services
     *
     * @return void
     */
    abstract public function start($services);

    /**
     * Stop the given services.
     *
     * @param string|array $services
     *
     * @return void
     */
    abstract public function stop($services);

    /**
     * Restart the given services.
     *
     * @param string|array $services
     *
     * @return void
     */
    abstract public function restart($services);

    /**
     * Print the status of the given services.
     *
     * @param string|array $services
     *
     * @return void
     */
    abstract public function printStatus($services);

    /**
     * Get the status of the given service.
     *
     * @param string $service
     *
     * @return string
     */
    abstract public function status($service);

    /**
     * Determine if the given service is disabled.
     *
     * @param string $service
     *
     * @return bool
     */
    abstract public function disabled($service);

    /**
     * Disable the given services.
     *
     * @param string|array $services
     *
     * @return void
     */
    abstract public function disable($services);

    /**
     * Enable the given services.
     *
     * @param string|array $services
     *
     * @return void
     */
    abstract public function enable($services);

    /**
     * Install and start DevSuite's DNS-merging service.
     *
     * @param CDevSuite_Filesystem $files
     *
     * @return void
     */
    abstract public function installDevSuiteDns($files);
}

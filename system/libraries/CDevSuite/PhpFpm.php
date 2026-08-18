<?php

/**
 * Description of PhpFpm.
 */
abstract class CDevSuite_PhpFpm {
    /**
     * Restart the PHP FPM process.
     *
     * @return void
     */
    abstract public function restart();

    /**
     * Install and configure PHP FPM.
     *
     * @return void
     */
    abstract public function install();

    /**
     * Forcefully uninstall PHP FPM and its DevSuite configuration.
     *
     * @return void
     */
    abstract public function uninstall();

    /**
     * Stop the PHP FPM process.
     *
     * @return void
     */
    abstract public function stop();
}

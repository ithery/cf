<?php

trait CTesting_Chrome_SupportChromeTrait {
    /**
     * The path to the custom Chromedriver binary.
     *
     * @var null|string
     */
    protected static $chromeDriver;

    /**
     * The Chromedriver process instance.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected static $chromeProcess;

    /**
     * Start the Chromedriver process.
     *
     * @param array $arguments
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public static function startChromeDriver(array $arguments = []) {
        static::$chromeProcess = static::buildChromeProcess($arguments);

        static::$chromeProcess->start();

        static::afterClass(function () {
            static::stopChromeDriver();
        });
    }

    /**
     * Stop the Chromedriver process.
     *
     * @return void
     */
    public static function stopChromeDriver() {
        if (static::$chromeProcess) {
            static::$chromeProcess->stop();
        }
    }

    /**
     * Build the process to run the Chromedriver.
     *
     * @param array $arguments
     *
     * @throws \RuntimeException
     *
     * @return \Symfony\Component\Process\Process
     */
    protected static function buildChromeProcess(array $arguments = []) {
        return (new CTesting_Chrome_ChromeProcess(static::$chromeDriver))->toProcess($arguments);
    }

    /**
     * Set the path to the custom Chromedriver.
     *
     * @param string $path
     *
     * @return void
     */
    public static function useChromedriver($path) {
        static::$chromeDriver = $path;
    }
}

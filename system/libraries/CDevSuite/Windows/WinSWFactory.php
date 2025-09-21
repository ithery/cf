<?php

class CDevSuite_Windows_WinSWFactory {
    /**
     * @var CommandLine
     */
    protected $cli;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new factory instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Make a new WinSW instance.
     *
     * @param string $service
     *
     * @return WinSW
     */
    public function make(string $service) {
        return new CDevSuite_Windows_WinSW(
            $service,
            $this->cli,
            $this->files
        );
    }
}

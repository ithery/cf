<?php

/**
 * Description of System
 *
 * @author Hery
 */
abstract class CDevSuite_System {
    public $cli;

    public $files;

    /**
     * Create a new System instance.
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Get the paths to all of the DevSuite extensions.
     *
     * @return array
     */
    public function extensions() {
        if (!$this->files->isDir(CDevSuite::homePath() . '/Extensions')) {
            return [];
        }

        return c::collect($this->files->scandir(CDevSuite::homePath() . '/Extensions'))
                        ->reject(function ($file) {
                            return is_dir($file);
                        })
                        ->map(function ($file) {
                            return CDevSuite::homePath() . '/Extensions/' . $file;
                        })
                        ->values()->all();
    }
}

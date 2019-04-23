<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 24, 2019, 1:09:54 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class CGit_Client {

    protected $path;

    public function __construct($path = null) {
        if (!$path) {
            $finder = new ExecutableFinder();
            $path = $finder->find('git', '/usr/bin/git');
        }
        $this->setPath($path);
    }

    /**
     * Opens a repository at the specified path.
     *
     * @param  string     $path Path where the repository is located
     *
     * @return CGit_Repository Instance of Repository
     */
    public function getRepository($path) {
        if (!file_exists($path) || !file_exists($path . '/.git/HEAD') && !file_exists($path . '/HEAD')) {
            throw new \RuntimeException('There is no GIT repository at ' . $path);
        }
        return new Repository($path, $this);
    }

}

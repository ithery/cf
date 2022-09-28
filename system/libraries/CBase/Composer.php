<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\PhpExecutableFinder;

class CBase_Composer {
    /**
     * The working path to regenerate from.
     *
     * @var null|string
     */
    protected $workingPath;

    /**
     * Create a new Composer manager instance.
     *
     * @param null|string $workingPath
     *
     * @return void
     */
    public function __construct($workingPath = null) {
        $this->workingPath = $workingPath;
    }

    /**
     * Regenerate the Composer autoloader files.
     *
     * @param string|array $extra
     *
     * @return int
     */
    public function dumpAutoloads($extra = '') {
        $extra = $extra ? (array) $extra : [];

        $command = array_merge($this->findComposer(), ['dump-autoload'], $extra);

        return $this->getProcess($command)->run();
    }

    /**
     * Regenerate the optimized Composer autoloader files.
     *
     * @return int
     */
    public function dumpOptimized() {
        return $this->dumpAutoloads('--optimize');
    }

    /**
     * Get the composer command for the environment.
     *
     * @return array
     */
    public function findComposer() {
        if (CFile::exists($this->workingPath . '/composer.phar')) {
            return [$this->phpBinary(), 'composer.phar'];
        }

        return ['composer'];
    }

    /**
     * Get the PHP binary.
     *
     * @return string
     */
    protected function phpBinary() {
        return CBase_ProcessUtils::escapeArgument((new PhpExecutableFinder())->find(false));
    }

    /**
     * Get a new Symfony process instance.
     *
     * @param array $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function getProcess(array $command) {
        return (new Process($command, $this->workingPath))->setTimeout(null);
    }

    /**
     * Set the working path used by the class.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setWorkingPath($path) {
        $this->workingPath = realpath($path);

        return $this;
    }

    /**
     * Get the version of Composer.
     *
     * @return null|string
     */
    public function getVersion() {
        $command = array_merge($this->findComposer(), ['-V', '--no-ansi']);

        $process = $this->getProcess($command);

        $process->run();

        $output = $process->getOutput();

        if (preg_match('/(\d+(\.\d+){2})/', $output, $version)) {
            return $version[1];
        }

        return explode(' ', $output)[2] ?? null;
    }
}

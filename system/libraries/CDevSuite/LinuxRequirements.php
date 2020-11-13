<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_LinuxRequirements {

    public $cli;
    public $files;
    public $ignoreSELinux = false;
    public $devsuiteBin = '/usr/local/bin/devsuite';
    public $sudoers = '/etc/sudoers.d/devsuite';

    /**
     * Create a new Warning instance.
     *
     * @param CommandLine $cli
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Determine if SELinux check should be skipped
     *
     * @param bool $ignore
     * @return $this
     */
    public function setIgnoreSELinux($ignore = true) {
        $this->ignoreSELinux = $ignore;

        return $this;
    }

    /**
     * Run all checks and output warnings.
     */
    public function check() {
        $this->homePathIsInsideRoot();
        $this->seLinuxIsEnabled();
    }

    /**
     * Verify if devsuite home is inside /root directory.
     *
     * This usually means the HOME parameters has not been
     * kept using sudo.
     */
    public function homePathIsInsideRoot() {
        if (strpos(CDevSuite::homePath(), '/root/') === 0) {
            throw new RuntimeException("devsuite home directory is inside /root");
        }
    }

    /**
     * Verify is SELinux is enabled and in enforcing mode.
     */
    public function seLinuxIsEnabled() {
        if ($this->ignoreSELinux) {
            return;
        }

        $output = $this->cli->run('sestatus');

        if (preg_match('@SELinux status:(\s+)enabled@', $output) && preg_match('@Current mode:(\s+)enforcing@', $output)
        ) {
            throw new RuntimeException("SELinux is in enforcing mode");
        }
    }

    /**
     * Symlink the Valet Bash script into the user's local bin.
     *
     * @return void
     */
    public function symlinkToUsersBin() {
        $this->cli->run('ln -snf ' . dirname(__DIR__, 2) . '/devsuite' . ' ' . $this->valetBin);
    }

    /**
     * Unlink the Valet Bash script from the user's local bin
     * and the sudoers.d entry
     *
     * @return void
     */
    public function uninstall() {
        $this->files->unlink($this->devsuiteBin);
        $this->files->unlink($this->sudoers);
    }

    
     /**
     * Get the paths to all of the DevSuite extensions.
     *
     * @return array
     */
    public function extensions()
    {
        if (!$this->files->isDir(CDevSuite::homePath() . '/Extensions')) {
            return [];
        }

        return c::collect($this->files->scandir(CDevSuite::homePath() . '/Extensions'))
            ->reject(static function ($file) {
                return is_dir($file);
            })
            ->map(static function ($file) {
                return CDevSuite::homePath() . '/Extensions/' . $file;
            })
            ->values()->all();
    }

}

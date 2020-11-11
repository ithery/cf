<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_LinuxRequirements {

    public $cli;
    public $ignoreSELinux = false;

    /**
     * Create a new Warning instance.
     *
     * @param CommandLine $cli
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
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
     * Verify if valet home is inside /root directory.
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

}

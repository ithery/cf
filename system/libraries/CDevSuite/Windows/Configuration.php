<?php

/**
 * Description of Configuration
 *
 * @author Hery
 */
class CDevSuite_Windows_Configuration extends CDevSuite_Configuration {
    /**
     * Install the DevSuite configuration file.
     *
     * @return void
     */
    public function install() {
        $this->createConfigurationDirectory();
        $this->createDriversDirectory();
        $this->createSitesDirectory();
        $this->createExtensionsDirectory();
        $this->createLogDirectory();
        $this->createCertificatesDirectory();
        $this->createServicesDirectory();
        $this->writeBaseConfiguration();

        $this->files->chown($this->path(), CDevSuite::user());
    }

    /**
     * Create the DevSuite configuration directory.
     *
     * @return void
     */
    public function createConfigurationDirectory() {
        $this->files->ensureDirExists(preg_replace('~/devsuite$~', '', CDevSuite::homePath()), CDevSuite::user());

        $oldPath = $_SERVER['HOME'] . '/.devsuite';

        if ($this->files->isDir($oldPath)) {
            rename($oldPath, CDevSuite::homePath());
            $this->prependPath(CDevSuite::homePath() . '/Sites');
        }

        $this->files->ensureDirExists(CDevSuite::homePath(), CDevSuite::user());
    }

    /**
     * Create the directory for the Windows services.
     *
     * @return void
     */
    public function createServicesDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Services', CDevSuite::user());
    }

    public function uninstall() {
        //do nothing on windows
    }
}

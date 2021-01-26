<?php

/**
 * Description of Configuration
 *
 * @author Hery
 */
class CDevSuite_Mac_Configuration extends CDevSuite_Configuration {
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
        $this->writeBaseConfiguration();

        $this->files->chown($this->path(), CDevSuite::user());
    }

    /**
     * Forcefully delete the DevSuite home configuration directory and contents.
     *
     * @return void
     */
    public function uninstall() {
        $this->files->unlink(CDevSuite::homePath());
    }

    /**
     * Create the DevSuite configuration directory.
     *
     * @return void
     */
    public function createConfigurationDirectory() {
        $this->files->ensureDirExists(preg_replace('~/devsuite$~', '', CDevSuite::homePath()), CDevSuite::user());

        $oldPath = posix_getpwuid(fileowner(__FILE__))['dir'] . '/.devsuite';

        if ($this->files->isDir($oldPath)) {
            rename($oldPath, CDevSuite::homePath());
            $this->prependPath(CDevSuite::homePath() . '/Sites');
        }

        $this->files->ensureDirExists(CDevSuite::homePath(), CDevSuite::user());
    }
}

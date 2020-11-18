<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Linux_Configuration extends CDevSuite_Configuration {

    /**
     * Uninstall the DevSuite configuration folder.
     *
     * @return void
     */
    public function uninstall() {
        if ($this->files->isDir(CDevSuite::homePath(), CDevSuite::user())) {
            $this->files->remove(CDevSuite::homePath());
        }
    }

    /**
     * Create the DevSuite configuration directory.
     *
     * @return void
     */
    public function createConfigurationDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath(), CDevSuite::user());
    }

    /**
     * Write the base, initial configuration for DevSuite.
     */
    public function writeBaseConfiguration() {
        if (!$this->files->exists($this->path())) {
            $this->write(['domain' => 'test', 'paths' => [], 'port' => '80']);
        }
    }

}

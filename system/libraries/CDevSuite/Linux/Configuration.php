<?php

/**
 * Description of Configuration.
 */
class CDevSuite_Linux_Configuration extends CDevSuite_Configuration {
    /**
     * Uninstall the DevSuite configuration folder.
     *
     * @return void
     */
    public function uninstall() {
        if ($this->files->isDir(CDevSuite::homePath())) {
            $this->files->remove(CDevSuite::homePath());
        }
    }

    /**
     * Write the base, initial configuration for DevSuite.
     *
     * @return void
     */
    public function writeBaseConfiguration() {
        if (!$this->files->exists($this->path())) {
            $this->write(['tld' => 'test', 'paths' => [], 'port' => '80']);
        }
    }
}

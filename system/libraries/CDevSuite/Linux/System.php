<?php

/**
 * Description of System
 *
 * @author Hery
 */
class CDevSuite_Linux_System extends CDevSuite_System {


    public $devSuiteBin = '/usr/local/bin/devsuite';
    public $sudoers = '/etc/sudoers.d/devsuite';

    /**
     * Symlink the DevSuite Bash script into the user's local bin.
     *
     * @return void
     */
    public function symlinkToUsersBin() {
        $this->cli->run('ln -snf ' . dirname(__DIR__, 2) . '/devsuite' . ' ' . $this->devSuiteBin);
    }

    /**
     * Unlink the DevSuite Bash script from the user's local bin
     * and the sudoers.d entry
     *
     * @return void
     */
    public function uninstall() {
        $this->files->unlink($this->devSuiteBin);
        $this->files->unlink($this->sudoers);
    }

}

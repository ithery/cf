<?php

/**
 * Description of System.
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
        $this->unlinkFromUsersBin();

        $this->cli->runAsUser('ln -s "' . realpath(__DIR__ . '/../../../data/devsuite/scripts/linux/bootstrap.sh') . '" ' . $this->devSuiteBin);
    }

    /**
     * Remove the symlink from the user's local bin.
     *
     * @return void
     */
    public function unlinkFromUsersBin() {
        $this->cli->quietlyAsUser('rm ' . $this->devSuiteBin);
    }

    /**
     * Unlink the DevSuite Bash script from the user's local bin
     * and the sudoers.d entry.
     *
     * @return void
     */
    public function uninstall() {
        $this->files->unlink($this->devSuiteBin);
        $this->files->unlink($this->sudoers);
    }
}

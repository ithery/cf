<?php

/**
 * Description of System
 *
 * @author Hery
 */
class CDevSuite_Mac_System extends CDevSuite_System {

    public $devSuiteBin = '/usr/local/bin/devsuite';

    /**
     * Symlink the Valet Bash script into the user's local bin.
     *
     * @return void
     */
    public function symlinkToUsersBin() {
        $this->unlinkFromUsersBin();

        $this->cli->runAsUser('ln -s "' . realpath(__DIR__ . '/../../valet') . '" ' . $this->valetBin);
    }

    /**
     * Remove the symlink from the user's local bin.
     *
     * @return void
     */
    public function unlinkFromUsersBin() {
        $this->cli->quietlyAsUser('rm ' . $this->valetBin);
    }

    /**
     * Create the "sudoers.d" entry for running Valet.
     *
     * @return void
     */
    public function createSudoersEntry() {
        $this->files->ensureDirExists('/etc/sudoers.d');

        $this->files->put('/etc/sudoers.d/devsuite', 'Cmnd_Alias DEVSUITE = /usr/local/bin/devsuite *
%admin ALL=(root) NOPASSWD:SETENV: DEVSUITE' . PHP_EOL);
    }

    /**
     * Remove the "sudoers.d" entry for running Valet.
     *
     * @return void
     */
    public function removeSudoersEntry() {
        $this->cli->quietly('rm /etc/sudoers.d/devsuite');
    }

}

<?php

/**
 * Description of System.
 */
class CDevSuite_Windows_System extends CDevSuite_System {
    /**
     * Symlink the DevSuite bootstrap script into the user's local bin.
     *
     * @return void
     *
     * @throws Exception always, as this is not implemented on Windows
     */
    public function symlinkToUsersBin() {
        throw new Exception('Not implemented');
    }
}

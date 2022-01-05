<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 16, 2019, 4:42:02 AM
 */
class CDaemon_Worker_Listener_SocketListener extends CDaemon_Worker_ListenerAbstract {
    public function checkEnvironment(array $errors = []) {
    }

    public function setup() {
        $this->user = $this->getCurrentUser();
        $this->event = new CDaemon_Worker_Event();
        // Set an empty onMessage callback.
        if (empty($this->onMessage)) {
            $this->onMessage = function () {
            };
        }
        $this->listen();
    }

    public function teardown() {
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

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

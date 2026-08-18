<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDaemon_Server_Listener_SocketListener extends CDaemon_Server_ListenerAbstract {
    public function checkEnvironment(array $errors = []) {
    }

    public function setup() {
        $this->user = $this->getCurrentUser();
        $this->event = new CDaemon_Server_Event();
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

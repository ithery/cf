<?php

class CConsole_Event_CFCliStarting {

    /**
     * The Console application instance.
     *
     * @var CConsole_Application
     */
    public $cfCli;

    /**
     * Create a new event instance.
     *
     * @param  CConsole_Application  $cfCli
     * @return void
     */
    public function __construct($cfCli) {
        $this->cfCli = $cfCli;
    }

}

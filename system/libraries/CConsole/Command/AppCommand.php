<?php

class CConsole_Command_AppCommand extends CConsole_Command {
    protected $prefix;

    public function __construct() {
        parent::__construct();
        $this->prefix = CF::config('app.prefix');

        if (strlen($this->prefix) == 0) {
            echo "Application prefix is required, make sure You on app directory. You can define it on app config using key \"prefix\"\n";
            exit;
        }
    }
}

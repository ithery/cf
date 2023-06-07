<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
class CConsole_Command_App_AppPresetCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:preset';

    public function handle() {
        CConsole::devSuiteRequired($this);
        CConsole::domainRequired($this);
        CConsole::prefixRequired($this);
    }
}

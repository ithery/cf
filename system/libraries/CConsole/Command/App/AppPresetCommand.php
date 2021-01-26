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
    protected $signature = 'app:preset {prefix}';

    public function handle() {
        $appCode = $this->argument('appCode');
        $prefix = $this->argument('prefix');

        echo $appCode;
    }
}

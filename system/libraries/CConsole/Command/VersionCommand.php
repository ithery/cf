<?php

use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class CConsole_Command_VersionCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version';

    public function handle() {
        $this->info(CF::version());
    }
}

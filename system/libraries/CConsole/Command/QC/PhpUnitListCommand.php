<?php

/**
 * Description of PhpUnitListCommand
 *
 * @author Hery
 */
use PHPUnit\TextUI\TestRunner;

class CConsole_Command_QC_PhpUnitListCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qc:phpunit:list';

    public function handle() {

        $this->info('show list of phpunit available');
        
    }

}

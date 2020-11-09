<?php

/**
 * Description of PhpUnitListCommand
 *
 * @author Hery
 */
use PHPUnit\TextUI\TestRunner;

class CQC_Console_Command_PhpUnitListCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qc:phpunit:list {--group=}';

    public function handle() {

        $this->info('show list of phpunit available');

        $group = $this->getOption('group');

        $qcManager = CQC_Manager::instance();


        $groupKeys = $qcManager->getUnitTestGroupsKey();

        if (strlen($group) > 0) {
            $groupKeys = [$group];
        }

        if (is_array($groupKeys) && count($groupKeys) > 0) {
            foreach ($groupKeys as $groupKey) {
                $this->info($groupKey);
            }
        }
    }

}

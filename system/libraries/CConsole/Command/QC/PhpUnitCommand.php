<?php

/**
 * Description of PhpUnitCommand
 *
 * @author Hery
 */
use PHPUnit\TextUI\TestRunner;

class CConsole_Command_QC_PhpUnitCommand extends CConsole_Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qc:phpunit {--class=}';

    public function handle() {
        $class = $this->option('class');
        $inspector = CQC::createInspector($class);
        $fileName = $inspector->getFileName();

        $this->info('Running test on file ' . $fileName);
        $runner = new TestRunner();
        $suite = $runner->getTest(TBQC_UnitTest_MemberApi_BasicTestMemberApi::class);
        try {
            $result = $runner->run($suite, [], [], true);
        } catch (Throwable $t) {
            $this->error($t->getMessage() . PHP_EOL);
        }


        $return = TestRunner::FAILURE_EXIT;

        if (isset($result) && $result->wasSuccessful()) {
            $return = TestRunner::SUCCESS_EXIT;
        } elseif (!isset($result) || $result->errorCount() > 0) {
            $return = TestRunner::EXCEPTION_EXIT;
        }
        return $return;
    }

}

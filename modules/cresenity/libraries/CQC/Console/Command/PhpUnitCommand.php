<?php

/**
 * Description of PhpUnitCommand
 *
 * @author Hery
 */
use PHPUnit\TextUI\TestRunner;

class CQC_Console_Command_PhpUnitCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qc:phpunit {--group=} {--class=} {--method=}';

    public function handle() {
        $class = $this->option('class');
        $method = $this->option('method');
        $group = $this->option('group');

        //start the session first
        CSession::instance();

        if (strlen($group) > 0) {
            //try to get all class from group
            $qcManager = CQC_Manager::instance();
            $listUnitTest = $qcManager->unitTests($group);
            $class = array_keys($listUnitTest);
        }
        $classArr = carr::wrap($class);

        $return = TestRunner::FAILURE_EXIT;
        foreach ($classArr as $class) {
            if (strlen($class) > 0) {
                if (!class_exists($class)) {
                    $this->error('Class not exists:' . $class);
                    $return = TestRunner::FAILURE_EXIT;
                }
            }
            $return = $this->testByClass($class, $method);
            if ($return != TestRunner::SUCCESS_EXIT) {
                break;
            }
        }

        return $return;
    }

    public function testByClass($class, $method = null) {
        $inspector = CQC::createInspector($class);
        $fileName = $inspector->getFileName();

        $return = TestRunner::FAILURE_EXIT;

        $this->info('Running test on file ' . $fileName);
        $runner = new TestRunner();
        $suite = $runner->getTest($class);
        try {
            $args = [];
            $args['verbose'] = $this->option('verbose');
            $args['debug'] = $this->option('verbose');
            if (strlen($method) > 0) {
                $args['filter'] = $method;
            }
            $result = $runner->run($suite, $args, [], true);
        } catch (Throwable $t) {
            $this->error($t->getMessage() . PHP_EOL . $t->getTraceAsString());
            $return = TestRunner::FAILURE_EXIT;
        }

        if (isset($result) && $result->wasSuccessful()) {
            $return = TestRunner::SUCCESS_EXIT;
        } elseif (!isset($result) || $result->errorCount() > 0) {
            $return = TestRunner::EXCEPTION_EXIT;
        }
        return $return;
        return true;
    }
}

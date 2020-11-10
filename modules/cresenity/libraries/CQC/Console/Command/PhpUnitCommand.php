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
    protected $signature = 'qc:phpunit {--class=} {--method=}';

    public function handle() {


        $class = $this->option('class');
        $method = $this->option('method');
        
        if (strlen($class) > 0) {
            if (!class_exists($class)) {
                $this->error('Class not exists:' . $class);
                return;
            }
        }
        
        $inspector = CQC::createInspector($class);
        $fileName = $inspector->getFileName();

        

        $this->info('Running test on file ' . $fileName);
        $runner = new TestRunner();
        $suite = $runner->getTest($class);
        try {
            $args = [];
            $args['verbose'] = $this->option('verbose');
            if(strlen($method)>0) {
                $args['filter']=$method;
            }
            $result = $runner->run($suite, $args, [], true);
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

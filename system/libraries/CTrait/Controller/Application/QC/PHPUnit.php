<?php

/**
 * Description of UnitTest.
 *
 * @author Hery
 */
trait CTrait_Controller_Application_QC_PHPUnit {
    protected function getTitle() {
        return 'Unit Test';
    }

    public function index() {
        $app = c::app();

        $app->title($this->getTitle());

        $phpUnit = CQC::manager()->phpUnit();
        $tests = [];
        foreach ($phpUnit->getTestSuites() as $testSuite) {
            foreach ($testSuite->getTestCases() as $testCase) {
                $test = $testCase->toArray();
                $test['time'] = null;
                $test['state'] = 'idle';
                $test['enabled'] = true;
                $test['suiteName'] = $testSuite->getName();
                $tests[] = $test;
            }
        }
        $runTestUrl = $this->controllerUrl() . 'run';
        $pollUrl = $this->controllerUrl() . 'poll';
        $app->addView('cresenity.qc.tests', [
            'tests' => $tests,
            'runTestUrl' => $runTestUrl,
            'pollUrl' => $pollUrl,

        ]);

        return $app;
    }

    public function run() {
    }

    public function poll() {
    }
}

<?php

trait CQC_Testing_Repository_Concern_RunTrait {
    /**
     * Delete all from runs table.
     */
    public function clearRuns() {
        CQC_Testing_Model_Run::truncate();
    }

    /**
     * Create a new run record for a test.
     *
     * @param $test
     *
     * @return mixed
     */
    public function createNewRunForTest($test) {
        return CQC_Testing_Model_Run::create([
            'test_id' => $test->test_id,
            'log' => '',
            'was_ok' => false,
        ]);
    }

    /**
     * Get test info.
     *
     * @param $test
     *
     * @return array
     */
    protected function getTestInfo($test) {
        $run = CQC_Testing_Model_Run::where('test_id', $test->id)->orderBy('created', 'desc')->first();

        return [
            'testId' => $test->test_id,
            'suiteName' => $test->suite->name,
            'path' => $test->path . DIRECTORY_SEPARATOR,
            'name' => $test->name,
            'updatedAt' => $test->updated->diffForHumans(null, false, true),
            'state' => $test->state,
            'enabled' => $test->enabled,
            'coverage' => ['enabled' => $test->suite->coverage_enabled, 'index' => $test->suite->coverage_index],

            'run' => $run,
            'notifiedAt' => is_null($run) ? null : $run->notified_at,
            'log' => is_null($run) ? null : $run->log,
            'html' => is_null($run) ? null : $run->html,
            'image' => is_null($run) ? null : $run->png,
            'time' => is_null($run) ? '' : (is_null($run->started_at) ? '' : $this->removeBefore($run->started_at->diffForHumans($run->ended_at))),
        ];
    }

    /**
     * Update the run log.
     *
     * @param $run
     * @param $output
     */
    public function updateRunLog($run, $output) {
        $run->log = $output;

        $run->save();
    }
}

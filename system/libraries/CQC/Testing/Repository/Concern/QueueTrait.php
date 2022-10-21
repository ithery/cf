<?php

use CQC_Testing_Model_Queue as QueueModel;

trait CQC_Testing_Repository_Concern_QueueTrait {
    /**
     * Is the test in the queue?
     *
     * @param $test
     *
     * @return bool
     */
    public function isEnqueued($test) {
        return
            $test->state == CQC_Testing::STATE_QUEUED
            && QueueModel::where('test_id', $test->id)->first();
    }

    /**
     * Queue all tests.
     */
    public function queueAllTests() {
        $this->showProgress('QUEUE: adding tests to queue...');

        foreach (CQC_Testing_Model_Test::all() as $test) {
            $this->addTestToQueue($test);
        }
    }

    /**
     * Queue all tests from a particular suite.
     *
     * @param $suite_id
     */
    public function queueTestsForSuite($suite_id) {
        $tests = CQC_Testing_Model_Test::where('suite_id', $suite_id)->get();

        foreach ($tests as $test) {
            $this->addTestToQueue($test);
        }
    }

    /**
     * Add a test to the queue.
     *
     * @param $test
     * @param bool $force
     */
    public function addTestToQueue($test, $force = false) {
        if ($test->enabled && $test->suite->project->enabled && !$this->isEnqueued($test)) {
            $test->updateSha1();

            QueueModel::updateOrCreate(['test_id' => $test->id]);

            // After queueing, if it's the only one, it may take the test and run it right away,
            // so we must wait a little for it to happen
            sleep(1);

            // We then get a fresh model, which may have a different state now
            $test = $test->fresh();

            if ($force || !in_array($test->state, [CQC_Testing::STATE_RUNNING, CQC_Testing::STATE_QUEUED])) {
                $test->state = CQC_Testing::STATE_QUEUED;

                $test->timestamps = false;

                $test->save();
            }
        }
    }

    /**
     * Get a test from the queue.
     *
     * @return null|\CQC_Testing_Model_Test
     */
    public function getNextTestFromQueue() {
        $query = QueueModel::join('test', 'test.test_id', '=', 'queue.test_id')
            ->where('test.enabled', true)
            ->where('test.state', '!=', CQC_Testing::STATE_RUNNING);

        if (!$queue = $query->first()) {
            return;
        }

        return $queue->test;
    }

    /**
     * Remove test from que run queue.
     *
     * @param $test
     *
     * @return mixed
     */
    protected function removeTestFromQueue($test) {
        QueueModel::where('test_id', $test->id)->delete();

        return $test;
    }

    /**
     * Reset a test to idle state.
     *
     * @param $test
     */
    protected function resetTest($test) {
        QueueModel::where('test_id', $test->id)->delete();

        $test->state = CQC_Testing::STATE_IDLE;

        $test->timestamps = false;

        $test->save();
    }
}

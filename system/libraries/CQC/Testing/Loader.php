<?php

class CQC_Testing_Loader {
    public function __construct(CQC_Testing_Repository $repository) {
        $this->dataRepository = $repository;
    }

    public function refreshSuites($data) {
        $this->dataRepository->removeMissingSuites($suites = $data['suites'], $project);

        c::collect($suites)->map(function ($data, $name) {
            $this->createSuite($name, $data);
        });
    }

    /**
     * Create or update the suite.
     *
     * @param $suite_name
     * @param $project
     * @param $suite_data
     */
    private function createSuite($suite_name, $suite_data) {
        $this->showProgress("  -- suite '{$suite_name}'");

        if (!$this->dataRepository->createOrUpdateSuite($suite_name, $suite_data)) {
            $this->displayMessages($this->dataRepository->getMessages());
            die;
        }
    }

    /**
     * Show progress in terminal.
     *
     * @param $line
     * @param mixed $type
     */
    public function showProgress($line, $type = 'line') {
        $this->command->{$type}($line);
    }

    /**
     * Show a comment in terminal.
     *
     * @param $comment
     */
    public function showComment($comment) {
        $this->command->comment($comment);
    }

    /**
     * Display messages in terminal.
     *
     * @param $messages
     */
    protected function displayMessages($messages) {
        $fatal = $messages->reduce(function ($carry, $message) {
            $prefix = $message['type'] == 'error' ? 'FATAL ERROR: ' : '';

            $this->command->{$message['type']}($prefix . $message['body']);

            if ($message['type'] == 'error') {
                return true;
            }

            return $carry;
        });

        if ($fatal == true) {
            die;
        }
    }
}

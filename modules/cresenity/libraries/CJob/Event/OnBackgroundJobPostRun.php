<?php


class CJob_Event_OnBackgroundJobPostRun {
    public $job;

    public $config;

    public $result;

    public function __construct($job, $config, $result) {
        $this->job = $job;
        $this->config = $config;
        $this->result = $result;
    }
}

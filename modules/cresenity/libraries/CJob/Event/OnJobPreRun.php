<?php


class CJob_Event_OnJobPreRun {
    public $job;

    public $config;

    public function __construct($job, $config) {
        $this->job = $job;
        $this->config = $config;
    }
}

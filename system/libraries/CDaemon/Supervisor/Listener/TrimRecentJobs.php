<?php

use Carbon\CarbonImmutable;

class CDaemon_Supervisor_Listener_TrimRecentJobs {
    /**
     * The last time the recent jobs were trimmed.
     *
     * @var \Carbon\CarbonImmutable
     */
    public $lastTrimmed;

    /**
     * How many minutes to wait in between each trim.
     *
     * @var int
     */
    public $frequency = 1;

    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_MasterSupervisorLooped $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_MasterSupervisorLooped $event) {
        if (!isset($this->lastTrimmed)) {
            $this->lastTrimmed = CarbonImmutable::now()->subMinutes($this->frequency + 1);
        }

        if ($this->lastTrimmed->lte(CarbonImmutable::now()->subMinutes($this->frequency))) {
            CDaemon_Supervisor::jobRepository()->trimRecentJobs();

            $this->lastTrimmed = CarbonImmutable::now();
        }
    }
}

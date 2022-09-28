<?php

interface CDaemon_Supervisor_Contract_WorkloadRepositoryInterface {
    /**
     * Get the current workload of each queue.
     *
     * @return array
     */
    public function get();
}

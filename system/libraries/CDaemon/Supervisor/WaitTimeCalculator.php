<?php

class CDaemon_Supervisor_WaitTimeCalculator {
    /**
     * The queue factory implementation.
     *
     * @var \CQueue_FactoryInterface
     */
    public $queue;

    /**
     * The supervisor repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_SupervisorRepositoryInterface
     */
    public $supervisors;

    /**
     * The metrics repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_MetricsRepositoryInterface
     */
    public $metrics;

    /**
     * Create a new calculator instance.
     *
     * @return void
     */
    public function __construct() {
        $this->queue = CQueue::queuer();
        $this->metrics = CDaemon_Supervisor::metricsRepository();
        $this->supervisors = CDaemon_Supervisor::supervisorRepository();
    }

    /**
     * Calculate the time to clear a given queue in seconds.
     *
     * @param string $queue
     *
     * @return float
     */
    public function calculateFor($queue) {
        return array_values($this->calculate($queue))[0] ?? 0;
    }

    /**
     * Calculate the time to clear per queue in seconds.
     *
     * @param null|string $queue
     *
     * @return array
     */
    public function calculate($queue = null) {
        $queues = $this->queueNames(
            $supervisors = c::collect($this->supervisors->all()),
            $queue
        );

        return $queues->mapWithKeys(function ($queue) use ($supervisors) {
            $totalProcesses = $this->totalProcessesFor($supervisors, $queue);

            list($connection, $queueName) = explode(':', $queue, 2);

            return [$queue => $this->calculateTimeToClear($connection, $queueName, $totalProcesses)];
        })->sort()->reverse()->all();
    }

    /**
     * Get all of the queue names.
     *
     * @param \CCollection $supervisors
     * @param null|string  $queue
     *
     * @return \CCollection
     */
    protected function queueNames($supervisors, $queue = null) {
        $queues = $supervisors->map(function ($supervisor) {
            return array_keys($supervisor->processes);
        })->collapse()->unique()->values();

        return $queue ? $queues->intersect([$queue]) : $queues;
    }

    /**
     * Get the total process count for a given queue.
     *
     * @param \CCollection $allSupervisors
     * @param string       $queue
     *
     * @return int
     */
    protected function totalProcessesFor($allSupervisors, $queue) {
        return $allSupervisors->sum(function ($supervisor) use ($queue) {
            return $supervisor->processes[$queue] ?? 0;
        });
    }

    /**
     * Calculate the time to clear for the given queue in seconds distributed over the given amount of processes.
     *
     * @param string $connection
     * @param string $queue
     * @param int    $totalProcesses
     *
     * @return int
     */
    public function calculateTimeToClear($connection, $queue, $totalProcesses) {
        $timeToClear = !cstr::contains($queue, ',')
            ? $this->timeToClearFor($connection, $queue)
            : c::collect(explode(',', $queue))->sum(function ($queueName) use ($connection) {
                return $this->timeToClearFor($connection, $queueName);
            });

        return $totalProcesses === 0
            ? round($timeToClear / 1000)
            : round(($timeToClear / $totalProcesses) / 1000);
    }

    /**
     * Get the total time to clear (in milliseconds) for a given queue.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return float
     */
    protected function timeToClearFor($connection, $queue) {
        $connection = $this->queue->connection($connection);
        /** @var CDaemon_Supervisor_Queue_RedisQueue $connection */
        $size = $connection->readyNow($queue);

        return $size * $this->metrics->runtimeForQueue($queue);
    }
}

<?php

class CDaemon_Supervisor_Repository_RedisWorkloadRepository implements CDaemon_Supervisor_Contract_WorkloadRepositoryInterface {
    /**
     * The queue factory implementation.
     *
     * @var \CQueue_FactoryInterface
     */
    public $queue;

    /**
     * The wait time calculator instance.
     *
     * @var \CDaemon_Supervisor_WaitTimeCalculator
     */
    public $waitTime;

    /**
     * The master supervisor repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_MasterSupervisorRepositoryInterface
     */
    private $masters;

    /**
     * The supervisor repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_SupervisorRepositoryInterface
     */
    private $supervisors;

    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct() {
        $this->queue = CQueue::queuer();
        $this->masters = CDaemon_Supervisor::masterSupervisorRepository();
        $this->waitTime = CDaemon_Supervisor::waitTimeCalculator();
        $this->supervisors = CDaemon_Supervisor::supervisorRepository();
    }

    /**
     * Get the current workload of each queue.
     *
     * @return array
     */
    public function get() {
        $processes = $this->processes();

        return c::collect($this->waitTime->calculate())
            ->map(function ($waitTime, $queue) use ($processes) {
                list($connection, $queueName) = explode(':', $queue, 2);

                $totalProcesses = $processes[$queue] ?? 0;

                $length = !cstr::contains($queue, ',')
                    ? c::collect([$queueName => $this->queue->connection($connection)->readyNow($queueName)])
                    : c::collect(explode(',', $queueName))->mapWithKeys(function ($queueName) use ($connection) {
                        return [$queueName => $this->queue->connection($connection)->readyNow($queueName)];
                    });

                $splitQueues = cstr::contains($queue, ',') ? $length->map(function ($length, $queueName) use ($connection, $totalProcesses, &$wait) {
                    return [
                        'name' => $queueName,
                        'length' => $length,
                        'wait' => $wait += $this->waitTime->calculateTimeToClear($connection, $queueName, $totalProcesses),
                    ];
                }) : null;

                return [
                    'name' => $queueName,
                    'length' => $length->sum(),
                    'wait' => $waitTime,
                    'processes' => $totalProcesses,
                    'split_queues' => $splitQueues,
                ];
            })->values()->toArray();
    }

    /**
     * Get the number of processes of each queue.
     *
     * @return array
     */
    private function processes() {
        return c::collect($this->supervisors->all())->pluck('processes')->reduce(function ($final, $queues) {
            foreach ($queues as $queue => $processes) {
                $final[$queue] = isset($final[$queue]) ? $final[$queue] + $processes : $processes;
            }

            return $final;
        }, []);
    }
}

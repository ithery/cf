<?php

class CDaemon_Supervisor_AutoScaler {
    /**
     * The queue factory implementation.
     *
     * @var \CQueue_FactoryInterface
     */
    public $queue;

    /**
     * The metrics repository implementation.
     *
     * @var \CDaemon_Supervisor_Contract_MetricsRepositoryInterface
     */
    public $metrics;

    /**
     * Create a new auto-scaler instance.
     *
     * @return void
     */
    public function __construct() {
        $this->queue = CQueue::queuer();
        $this->metrics = CDaemon_Supervisor::metricsRepository();
    }

    /**
     * Balance the workers on the given supervisor.
     *
     * @param \CDaemon_Supervisor_Supervisor $supervisor
     *
     * @return void
     */
    public function scale(CDaemon_Supervisor_Supervisor $supervisor) {
        $pools = $this->poolsByQueue($supervisor);

        $workers = $this->numberOfWorkersPerQueue(
            $supervisor,
            $this->timeToClearPerQueue($supervisor, $pools)
        );

        $workers->each(function ($workers, $queue) use ($supervisor, $pools) {
            $this->scalePool($supervisor, $pools[$queue], $workers);
        });
    }

    /**
     * Get the process pools keyed by their queue name.
     *
     * @param \CDaemon_Supervisor_Supervisor $supervisor
     *
     * @return \CCollection
     */
    protected function poolsByQueue(CDaemon_Supervisor_Supervisor $supervisor) {
        return $supervisor->processPools->mapWithKeys(function ($pool) {
            return [$pool->queue() => $pool];
        });
    }

    /**
     * Get the times in milliseconds needed to clear the queues.
     *
     * @param \CDaemon_Supervisor_Supervisor $supervisor
     * @param \CCollection                   $pools
     *
     * @return \CCollection
     */
    protected function timeToClearPerQueue(CDaemon_Supervisor_Supervisor $supervisor, CCollection $pools) {
        return $pools->mapWithKeys(function ($pool, $queue) use ($supervisor) {
            $size = $this->queue->connection($supervisor->options->connection)->readyNow($queue);

            return [$queue => [
                'size' => $size,
                'time' => ($size * $this->metrics->runtimeForQueue($queue)),
            ]];
        });
    }

    /**
     * Get the number of workers needed per queue for proper balance.
     *
     * @param \CDaemon_Supervisor_Supervisor $supervisor
     * @param \CCollection                   $queues
     *
     * @return \CCollection
     */
    protected function numberOfWorkersPerQueue(CDaemon_Supervisor_Supervisor $supervisor, CCollection $queues) {
        $timeToClearAll = $queues->sum('time');

        return $queues->mapWithKeys(function ($timeToClear, $queue) use ($supervisor, $timeToClearAll) {
            if ($timeToClearAll > 0
                && $supervisor->options->autoScaling()
            ) {
                return [$queue => (($timeToClear['time'] / $timeToClearAll) * $supervisor->options->maxProcesses)];
            } elseif ($timeToClearAll == 0
                && $supervisor->options->autoScaling()
            ) {
                return [
                    $queue => $timeToClear['size']
                                ? $supervisor->options->maxProcesses
                                : $supervisor->options->minProcesses,
                ];
            }

            return [$queue => $supervisor->options->maxProcesses / count($supervisor->processPools)];
        })->sort();
    }

    /**
     * Scale the given pool to the recommended number of workers.
     *
     * @param \CDaemon_Supervisor_Supervisor  $supervisor
     * @param \CDaemon_Supervisor_ProcessPool $pool
     * @param float                           $workers
     *
     * @return void
     */
    protected function scalePool(CDaemon_Supervisor_Supervisor $supervisor, $pool, $workers) {
        $supervisor->pruneTerminatingProcesses();

        $totalProcessCount = $pool->totalProcessCount();

        $desiredProcessCount = ceil($workers);

        if ($desiredProcessCount > $totalProcessCount) {
            $maxUpShift = min(
                $supervisor->options->maxProcesses - $supervisor->totalProcessCount(),
                $supervisor->options->balanceMaxShift
            );

            $pool->scale(
                min(
                    $totalProcessCount + $maxUpShift,
                    $supervisor->options->maxProcesses - (($supervisor->processPools->count() - 1) * $supervisor->options->minProcesses),
                    $desiredProcessCount
                )
            );
        } elseif ($desiredProcessCount < $totalProcessCount) {
            $maxDownShift = min(
                $supervisor->totalProcessCount() - $supervisor->options->minProcesses,
                $supervisor->options->balanceMaxShift
            );

            $pool->scale(
                max(
                    $totalProcessCount - $maxDownShift,
                    $supervisor->options->minProcesses,
                    $desiredProcessCount
                )
            );
        }
    }
}

<?php

use Carbon\CarbonImmutable;

class CDaemon_Supervisor_TaskQueue_RetryFailedJob {
    /**
     * The job ID.
     *
     * @var string
     */
    public $id;

    /**
     * Create a new job instance.
     *
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $queue = CQueue::queuer();
        $jobs = CDaemon::supervisor()->jobRepository();
        if (is_null($job = $jobs->findFailed($this->id))) {
            return;
        }

        $queue->connection($job->connection)->pushRaw(
            $this->preparePayload($id = cstr::uuid(), $job->payload),
            $job->queue
        );

        $jobs->storeRetryReference($this->id, $id);
    }

    /**
     * Prepare the payload for queueing.
     *
     * @param string $id
     * @param string $payload
     *
     * @return string
     */
    protected function preparePayload($id, $payload) {
        $payload = json_decode($payload, true);

        return json_encode(array_merge($payload, [
            'id' => $id,
            'uuid' => $id,
            'attempts' => 0,
            'retry_of' => $this->id,
            'retryUntil' => $this->prepareNewTimeout($payload),
        ]));
    }

    /**
     * Prepare the timeout.
     *
     * @param array $payload
     *
     * @return null|int
     */
    protected function prepareNewTimeout($payload) {
        $retryUntil = $payload['retryUntil'] ?? $payload['timeoutAt'] ?? null;

        $pushedAt = $payload['pushedAt'] ?? microtime(true);

        return $retryUntil
            ? CarbonImmutable::now()->addSeconds(ceil($retryUntil - $pushedAt))->getTimestamp()
            : null;
    }
}

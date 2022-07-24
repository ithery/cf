<?php

class CDaemon_Supervisor_Queue_RedisQueue extends CQueue_Queue_RedisQueue {
    /**
     * The job that last pushed to queue via the "push" method.
     *
     * @var object|string
     */
    protected $lastPushed;

    /**
     * Get the number of queue jobs that are ready to process.
     *
     * @param null|string $queue
     *
     * @return int
     */
    public function readyNow($queue = null) {
        return $this->getConnection()->llen($this->getQueue($queue));
    }

    /**
     * Push a new job onto the queue.
     *
     * @param object|string $job
     * @param mixed         $data
     * @param null|string   $queue
     *
     * @return mixed
     */
    public function push($job, $data = '', $queue = null) {
        return $this->enqueueUsing(
            $job,
            $this->createPayload($job, $this->getQueue($queue), $data),
            $queue,
            null,
            function ($payload, $queue) use ($job) {
                $this->lastPushed = $job;

                return $this->pushRaw($payload, $queue);
            }
        );
    }

    /**
     * Push a raw payload onto the queue.
     *
     * @param string $payload
     * @param string $queue
     * @param array  $options
     *
     * @return mixed
     */
    public function pushRaw($payload, $queue = null, array $options = []) {
        $payload = (new CDaemon_Supervisor_Queue_JobPayload($payload))->prepare($this->lastPushed);

        parent::pushRaw($payload->value, $queue, $options);

        $this->event($this->getQueue($queue), new CDaemon_Supervisor_Event_RedisEvent_JobPushed($payload->value));

        return $payload->id();
    }

    /**
     * Create a payload string from the given job and data.
     *
     * @param string $job
     * @param string $queue
     * @param mixed  $data
     *
     * @return array
     */
    protected function createPayloadArray($job, $queue, $data = '') {
        $payload = parent::createPayloadArray($job, $queue, $data);

        $payload['id'] = $payload['uuid'];

        return $payload;
    }

    /**
     * Push a new job onto the queue after a delay.
     *
     * @param \DateTimeInterface|\DateInterval|int $delay
     * @param string                               $job
     * @param mixed                                $data
     * @param string                               $queue
     *
     * @return mixed
     */
    public function later($delay, $job, $data = '', $queue = null) {
        $payload = (new CDaemon_Supervisor_Queue_JobPayload($this->createPayload($job, $queue, $data)))->prepare($job)->value;

        if (method_exists($this, 'enqueueUsing')) {
            return $this->enqueueUsing(
                $job,
                $payload,
                $queue,
                $delay,
                function ($payload, $queue, $delay) {
                    return c::tap(parent::laterRaw($delay, $payload, $queue), function () use ($payload, $queue) {
                        $this->event($this->getQueue($queue), new CDaemon_Supervisor_Event_RedisEvent_JobPushed($payload));
                    });
                }
            );
        }

        return c::tap(parent::laterRaw($delay, $payload, $queue), function () use ($payload, $queue) {
            $this->event($this->getQueue($queue), new CDaemon_Supervisor_Event_RedisEvent_JobPushed($payload));
        });
    }

    /**
     * Pop the next job off of the queue.
     *
     * @param string $queue
     *
     * @return null|\CQueue_JobInterface
     */
    public function pop($queue = null) {
        CDaemon::log('RedisQueue:Reserved');

        return c::tap(parent::pop($queue), function ($result) use ($queue) {
            if ($result) {
                $this->event($this->getQueue($queue), new CDaemon_Supervisor_Event_RedisEvent_JobReserved($result->getReservedJob()));
            }
        });
    }

    /**
     * Migrate the delayed jobs that are ready to the regular queue.
     *
     * @param string $from
     * @param string $to
     *
     * @return void
     */
    public function migrateExpiredJobs($from, $to) {
        return c::tap(parent::migrateExpiredJobs($from, $to), function ($jobs) use ($to) {
            $this->event($to, new CDaemon_Supervisor_Event_JobsMigrated($jobs));
        });
    }

    /**
     * Delete a reserved job from the queue.
     *
     * @param string               $queue
     * @param \CQueue_Job_RedisJob $job
     *
     * @return void
     */
    public function deleteReserved($queue, $job) {
        parent::deleteReserved($queue, $job);

        $this->event($this->getQueue($queue), new CDaemon_Supervisor_Event_RedisEvent_JobDeleted($job, $job->getReservedJob()));
    }

    /**
     * Delete a reserved job from the reserved queue and release it.
     *
     * @param string               $queue
     * @param \CQueue_Job_RedisJob $job
     * @param int                  $delay
     *
     * @return void
     */
    public function deleteAndRelease($queue, $job, $delay) {
        parent::deleteAndRelease($queue, $job, $delay);

        $this->event($this->getQueue($queue), new CDaemon_Supervisor_Event_RedisEvent_JobReleased($job->getReservedJob()));
    }

    /**
     * Fire the given event if a dispatcher is bound.
     *
     * @param string $queue
     * @param mixed  $event
     *
     * @return void
     */
    protected function event($queue, $event) {
        $queue = cstr::replaceFirst('queues:', '', $queue);

        CDaemon::log('connection:' . $this->getConnectionName());
        CDaemon::log('queue:' . $queue);
        CDaemon::log('event:' . get_class($event));
        CEvent::dispatcher()->dispatch(
            $event->connection($this->getConnectionName())->queue($queue)
        );
    }
}

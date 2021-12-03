<?php
use Closure;
use RuntimeException;
use DateTimeInterface;
use Illuminate\Support\Str;
use Illuminate\Queue\CallQueuedClosure;

class CConsole_Schedule {
    use CTrait_Macroable;

    const SUNDAY = 0;

    const MONDAY = 1;

    const TUESDAY = 2;

    const WEDNESDAY = 3;

    const THURSDAY = 4;

    const FRIDAY = 5;

    const SATURDAY = 6;

    /**
     * All of the events on the schedule.
     *
     * @var \CConsole_Schedule_Event[]
     */
    protected $events = [];

    /**
     * The event mutex implementation.
     *
     * @var \CConsole_Schedule_Contract_EventMutexInterface
     */
    protected $eventMutex;

    /**
     * The scheduling mutex implementation.
     *
     * @var \CConsole_Schedule_SchedulingMutex
     */
    protected $schedulingMutex;

    /**
     * The timezone the date should be evaluated on.
     *
     * @var \DateTimeZone|string
     */
    protected $timezone;

    /**
     * The job dispatcher implementation.
     *
     * @var \CQueue_Dispatcher
     */
    protected $dispatcher;

    /**
     * Create a new schedule instance.
     *
     * @param null|\DateTimeZone|string $timezone
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    public function __construct($timezone = null) {
        $this->timezone = $timezone;

        $container = c::container();

        $this->eventMutex = $container->bound(CConsole_Schedule_EventMutex::class)
                                ? $container->make(CConsole_Schedule_EventMutex::class)
                                : $container->make(CConsole_Schedule_CacheEventMutex::class);

        $this->schedulingMutex = $container->bound(CConsole_Schedule_SchedulingMutex::class)
                                ? $container->make(CConsole_Schedule_SchedulingMutex::class)
                                : $container->make(CConsole_Schedule_CacheSchedulingMutex::class);
    }

    /**
     * Add a new callback event to the schedule.
     *
     * @param string|callable $callback
     * @param array           $parameters
     *
     * @return \CConsole_Schedule_CallbackEvent
     */
    public function call($callback, array $parameters = []) {
        $this->events[] = $event = new CConsole_Schedule_CallbackEvent(
            $this->eventMutex,
            $callback,
            $parameters,
            $this->timezone
        );

        return $event;
    }

    /**
     * Add a new Artisan command event to the schedule.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return \Illuminate\Console\Scheduling\Event
     */
    public function command($command, array $parameters = []) {
        if (class_exists($command)) {
            $command = c::container()->make($command)->getName();
        }

        return $this->exec(
            CConsole_Application::formatCommandString($command),
            $parameters
        );
    }

    /**
     * Add a new job callback event to the schedule.
     *
     * @param object|string $job
     * @param null|string   $queue
     * @param null|string   $connection
     *
     * @return \CConsole_Schedule_CallbackEvent
     */
    public function job($job, $queue = null, $connection = null) {
        return $this->call(function () use ($job, $queue, $connection) {
            $job = is_string($job) ? c::container()->make($job) : $job;

            if ($job instanceof CQueue_ShouldQueueInterface) {
                $this->dispatchToQueue($job, $queue ?? $job->queue, $connection ?? $job->connection);
            } else {
                $this->dispatchNow($job);
            }
        })->name(is_string($job) ? $job : get_class($job));
    }

    /**
     * Dispatch the given job to the queue.
     *
     * @param object      $job
     * @param null|string $queue
     * @param null|string $connection
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function dispatchToQueue($job, $queue, $connection) {
        if ($job instanceof Closure) {
            if (!class_exists(CallQueuedClosure::class)) {
                throw new RuntimeException(
                    'To enable support for closure jobs, please install the illuminate/queue package.'
                );
            }

            $job = CQueue_CallQueuedClosure::create($job);
        }

        if ($job instanceof CQueue_Contract_ShouldBeUniqueInterface) {
            return $this->dispatchUniqueJobToQueue($job, $queue, $connection);
        }

        $this->getDispatcher()->dispatch(
            $job->onConnection($connection)->onQueue($queue)
        );
    }

    /**
     * Dispatch the given unique job to the queue.
     *
     * @param object      $job
     * @param null|string $queue
     * @param null|string $connection
     *
     * @throws \RuntimeException
     *
     * @return void
     */
    protected function dispatchUniqueJobToQueue($job, $queue, $connection) {
        if (!(new CQueue_UniqueLock(CCache::manager()->driver()))->acquire($job)) {
            return;
        }

        $this->getDispatcher()->dispatch(
            $job->onConnection($connection)->onQueue($queue)
        );
    }

    /**
     * Dispatch the given job right now.
     *
     * @param object $job
     *
     * @return void
     */
    protected function dispatchNow($job) {
        $this->getDispatcher()->dispatchNow($job);
    }

    /**
     * Add a new command event to the schedule.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return \CConsole_Schedule_Event
     */
    public function exec($command, array $parameters = []) {
        if (count($parameters)) {
            $command .= ' ' . $this->compileParameters($parameters);
        }

        $this->events[] = $event = new CConsole_Schedule_Event($this->eventMutex, $command, $this->timezone);

        return $event;
    }

    /**
     * Compile parameters for a command.
     *
     * @param array $parameters
     *
     * @return string
     */
    protected function compileParameters(array $parameters) {
        return c::collect($parameters)->map(function ($value, $key) {
            if (is_array($value)) {
                return $this->compileArrayInput($key, $value);
            }

            if (!is_numeric($value) && !preg_match('/^(-.$|--.*)/i', $value)) {
                $value = CBase_ProcessUtils::escapeArgument($value);
            }

            return is_numeric($key) ? $value : "{$key}={$value}";
        })->implode(' ');
    }

    /**
     * Compile array input for a command.
     *
     * @param string|int $key
     * @param array      $value
     *
     * @return string
     */
    public function compileArrayInput($key, $value) {
        $value = c::collect($value)->map(function ($value) {
            return CBase_ProcessUtils::escapeArgument($value);
        });

        if (cstr::startsWith($key, '--')) {
            $value = $value->map(function ($value) use ($key) {
                return "{$key}={$value}";
            });
        } elseif (cstr::startsWith($key, '-')) {
            $value = $value->map(function ($value) use ($key) {
                return "{$key} {$value}";
            });
        }

        return $value->implode(' ');
    }

    /**
     * Determine if the server is allowed to run this event.
     *
     * @param \CConsole_Schedule_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function serverShouldRun(CConsole_Schedule_Event $event, DateTimeInterface $time) {
        return $this->schedulingMutex->create($event, $time);
    }

    /**
     * Get all of the events on the schedule that are due.
     *
     * @param \CBase_ApplicationInterface $app
     *
     * @return \CCollection
     */
    public function dueEvents($app) {
        return c::collect($this->events)->filter->isDue($app);
    }

    /**
     * Get all of the events on the schedule.
     *
     * @return \CConsole_Schedule_Event[]
     */
    public function events() {
        return $this->events;
    }

    /**
     * Specify the cache store that should be used to store mutexes.
     *
     * @param string $store
     *
     * @return $this
     */
    public function useCache($store) {
        if ($this->eventMutex instanceof CConsole_Schedule_Contract_CacheAwareInterface) {
            $this->eventMutex->useStore($store);
        }

        if ($this->schedulingMutex instanceof CConsole_Schedule_Contract_CacheAwareInterface) {
            $this->schedulingMutex->useStore($store);
        }

        return $this;
    }

    /**
     * Get the job dispatcher, if available.
     *
     * @throws \RuntimeException
     *
     * @return \CQueue_DispatcherInterface
     */
    protected function getDispatcher() {
        return CQueue::dispatcher();
    }
}

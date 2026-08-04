<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @mixin CQueue_QueueInterface
 */
class CQueue_Manager implements CQueue_FactoryInterface, CQueue_Contract_MonitorInterface {
    use CQueue_Trait_ResolvesQueueRoutesTrait;

    /**
     * The array of resolved queue connections.
     *
     * @var array<CQueue_QueueInterface>
     */
    protected $connections = [];

    /**
     * The array of resolved queue connectors.
     *
     * @var array
     */
    protected $connectors = [];

    /**
     * The event dispatcher instance.
     *
     * @var CEvent_DispatcherInterface
     */
    protected $dispatcher;

    /**
     * The default driver name.
     *
     * @var string
     */
    protected $defaultDriver;

    /**
     * Create a new queue manager instance.
     *
     * @return void
     */
    public function __construct(CEvent_Dispatcher $dispatcher = null) {
        if ($dispatcher == null) {
            $dispatcher = CEvent::dispatcher();
        }
        $this->dispatcher = $dispatcher;

        $this->defaultDriver = CF::config('queue.default');
    }

    /**
     * Register an event listener for the before job event.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function before($callback) {
        $this->dispatcher->listen(CQueue_Event_JobProcessing::class, $callback);
    }

    /**
     * Register an event listener for the after job event.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function after($callback) {
        $this->dispatcher->listen(CQueue_Event_JobProcessed::class, $callback);
    }

    /**
     * Register an event listener for the exception occurred job event.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function exceptionOccurred($callback) {
        $this->dispatcher->listen(CQueue_Event_JobExceptionOccurred::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue starting.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function starting($callback) {
        $this->dispatcher->listen(CQueue_Event_WorkerStarting::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue loop.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function looping($callback) {
        $this->dispatcher->listen(CQueue_Event_Looping::class, $callback);
    }

    /**
     * Register an event listener for the failed job event.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function failing($callback) {
        $this->dispatcher->listen(CQueue_Event_JobFailed::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue stopping.
     *
     * @param mixed $callback
     *
     * @return void
     */
    public function stopping($callback) {
        $this->dispatcher->listen(CQueue_Event_WorkerStopping::class, $callback);
    }

    /**
     * Determine if the driver is connected.
     *
     * @param null|string $name
     *
     * @return bool
     */
    public function connected($name = null) {
        return isset($this->connections[$name ?: $this->getDefaultDriver()]);
    }

    /**
     * Resolve a queue connection instance.
     *
     * @param null|string $name
     *
     * @return \CQueue_QueueInterface
     */
    public function connection($name = null) {
        $name = $name ?: $this->getDefaultDriver();
        // If the connection has not been resolved yet we will resolve it now as all
        // of the connections are resolved when they are actually needed so we do
        // not make any unnecessary connection to the various queue end-points.

        if (!isset($this->connections[$name])) {
            $this->connections[$name] = $this->resolve($name);
            $this->connections[$name]->setContainer(CContainer::getInstance());
        }

        return $this->connections[$name];
    }

    /**
     * Resolve a queue connection.
     *
     * @param string $name
     *
     * @return CQueue_QueueInterface
     */
    protected function resolve($name) {
        $config = $this->getConfig($name);
        //tanpa penjagaan ini sebuah nama koneksi yang salah ketik berakhir
        //sebagai "Trying to access array offset on value of type null", yang
        //tidak menyebut nama koneksinya sama sekali
        if (is_null($config)) {
            throw new InvalidArgumentException("The [{$name}] queue connection has not been configured.");
        }

        return $this->getConnector($config['driver'])
            ->connect($config)
            ->setConnectionName($name);
    }

    /**
     * Get the connector for a given driver.
     *
     * @param string $driver
     *
     * @throws \InvalidArgumentException
     *
     * @return CQueue_AbstractConnector
     */
    protected function getConnector($driver) {
        if (!isset($this->connectors[$driver])) {
            throw new InvalidArgumentException("No connector for [{$driver}]");
        }

        return call_user_func($this->connectors[$driver]);
    }

    /**
     * Add a queue connection resolver.
     *
     * @param string   $driver
     * @param \Closure $resolver
     *
     * @return void
     */
    public function extend($driver, Closure $resolver) {
        return $this->addConnector($driver, $resolver);
    }

    /**
     * Add a queue connection resolver.
     *
     * @param string   $driver
     * @param \Closure $resolver
     *
     * @return void
     */
    public function addConnector($driver, Closure $resolver) {
        $this->connectors[$driver] = $resolver;
    }

    /**
     * Get the queue connection configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name) {
        if (!is_null($name) && $name !== 'null') {
            return CQueue::config("connections.{$name}");
        }

        return ['driver' => 'null'];
    }

    /**
     * Get the name of the default queue connection.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return $this->defaultDriver ?: 'database';
    }

    /**
     * Set the name of the default queue connection.
     *
     * @param string $name
     *
     * @return $this
     */
    public function setDefaultDriver($name) {
        $this->defaultDriver = $name;

        return $this;
    }

    /**
     * Get the full name for the given connection.
     *
     * @param null|string $connection
     *
     * @return string
     */
    public function getName($connection = null) {
        return $connection ?: $this->getDefaultDriver();
    }

    /**
     * Cache key holding the paused state of one queue.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return string
     */
    protected function pausedCacheKey($connection, $queue) {
        return 'cresenity:queue:paused:' . $connection . ':' . $queue;
    }

    /**
     * Pause a queue by its connection and name.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return void
     */
    public function pause($connection, $queue) {
        c::cache()->store()->forever($this->pausedCacheKey($connection, $queue), true);
        $this->dispatcher->dispatch(new CQueue_Event_QueuePaused($connection, $queue));
    }

    /**
     * Pause a queue by its connection and name for a given amount of time.
     *
     * @param string                      $connection
     * @param string                      $queue
     * @param DateInterval|DateTime|int $ttl
     *
     * @return void
     */
    public function pauseFor($connection, $queue, $ttl) {
        c::cache()->store()->put($this->pausedCacheKey($connection, $queue), true, $ttl);
        $this->dispatcher->dispatch(new CQueue_Event_QueuePaused($connection, $queue, $ttl));
    }

    /**
     * Resume a paused queue by its connection and name.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return void
     */
    public function resume($connection, $queue) {
        c::cache()->store()->forget($this->pausedCacheKey($connection, $queue));
        $this->dispatcher->dispatch(new CQueue_Event_QueueResumed($connection, $queue));
    }

    /**
     * Determine if a queue is paused.
     *
     * @param string $connection
     * @param string $queue
     *
     * @return bool
     */
    public function isPaused($connection, $queue) {
        return (bool) c::cache()->store()->get($this->pausedCacheKey($connection, $queue), false);
    }

    /**
     * Determine which of the given queues are currently paused.
     *
     * @param string $connection
     * @param array  $queueList
     *
     * @return array
     */
    public function getPausedQueues($connection, $queueList) {
        $keyList = [];
        foreach ($queueList as $queue) {
            $keyList[$queue] = $this->pausedCacheKey($connection, $queue);
        }
        $stateList = c::cache()->store()->many(array_values($keyList));
        $pausedList = [];
        foreach ($keyList as $queue => $key) {
            if (carr::get($stateList, $key, false)) {
                $pausedList[] = $queue;
            }
        }

        return $pausedList;
    }

    /**
     * Dynamically pass calls to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->connection()->$method(...$parameters);
    }
}

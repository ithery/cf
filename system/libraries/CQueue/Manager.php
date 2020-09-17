<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2019, 4:03:10 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * @mixin CQueue_QueueInterface
 */
class CQueue_Manager implements CQueue_FactoryInterface, CQueue_MonitorInterface {

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $connections = [];

    /**
     * The array of resolved queue connectors.
     *
     * @var array
     */
    protected $connectors = [];

    /**
     * The event dispatcher instance
     *
     * @var array
     */
    protected $dispatcher;    
    /**
     * Create a new queue manager instance.
     *
     * @return void
     */
    public function __construct(CEvent_Dispatcher $dispatcher= null) {
        if($dispatcher==null) {
            $dispatcher=CEvent::dispatcher();
        }
        $this->dispatcher = $dispatcher;
    }

    /**
     * Register an event listener for the before job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function before($callback) {
        $this->dispatcher->listen(CQueue_Event_JobProcessing::class, $callback);
    }

    /**
     * Register an event listener for the after job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function after($callback) {
        $this->dispatcher->listen(CQueue_Event_JobProcessed::class, $callback);
    }

    /**
     * Register an event listener for the exception occurred job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function exceptionOccurred($callback) {
        $this->dispatcher->listen(CQueue_Event_JobExceptionOccurred::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue loop.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function looping($callback) {
        $this->dispatcher->listen(CQueue_Event_Looping::class, $callback);
    }

    /**
     * Register an event listener for the failed job event.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function failing($callback) {
        $this->dispatcher->listen(CQueue_Event_JobFailed::class, $callback);
    }

    /**
     * Register an event listener for the daemon queue stopping.
     *
     * @param  mixed  $callback
     * @return void
     */
    public function stopping($callback) {
        $this->dispatcher->listen(CQueue_Event_WorkerStopping::class, $callback);
    }

    /**
     * Determine if the driver is connected.
     *
     * @param  string|null  $name
     * @return bool
     */
    public function connected($name = null) {
        return isset($this->connections[$name ?: $this->getDefaultDriver()]);
    }

    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \Illuminate\Contracts\Queue\Queue
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
     * @param  string  $name
     * @return CQueue_QueueInterface
     */
    protected function resolve($name) {
            
         
        
        $config = $this->getConfig($name);
        
        return $this->getConnector($config['driver'])
                        ->connect($config)
                        ->setConnectionName($name);
    }

    /**
     * Get the connector for a given driver.
     *
     * @param  string  $driver
     * @return CQueue_AbstractConnector
     *
     * @throws \InvalidArgumentException
     */
    protected function getConnector($driver) {
        if (!isset($this->connectors[$driver])) {
            throw new InvalidArgumentException("No connector for [$driver]");
        }
        return call_user_func($this->connectors[$driver]);
    }

    /**
     * Add a queue connection resolver.
     *
     * @param  string    $driver
     * @param  \Closure  $resolver
     * @return void
     */
    public function extend($driver, Closure $resolver) {
        return $this->addConnector($driver, $resolver);
    }

    /**
     * Add a queue connection resolver.
     *
     * @param  string    $driver
     * @param  \Closure  $resolver
     * @return void
     */
    public function addConnector($driver, Closure $resolver) {
        $this->connectors[$driver] = $resolver;
    }

    /**
     * Get the queue connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name) {
        if (! is_null($name) && $name !== 'null') {
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
        return CQueue::config('default','database');
    }

    /**
     * Set the name of the default queue connection.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name) {
        $this->app['config']['queue.default'] = $name;
    }

    /**
     * Get the full name for the given connection.
     *
     * @param  string|null  $connection
     * @return string
     */
    public function getName($connection = null) {
        return $connection ?: $this->getDefaultDriver();
    }

    /**
     * Dynamically pass calls to the default connection.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->connection()->$method(...$parameters);
    }

}

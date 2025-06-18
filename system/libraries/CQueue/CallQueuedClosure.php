<?php

class CQueue_CallQueuedClosure implements CQueue_ShouldQueueInterface {
    use CQueue_Trait_BatchableTrait;
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_SerializesModels;

    /**
     * The serializable Closure instance.
     *
     * @var \CQueue_SerializableClosure
     */
    public $closure;

    /**
     * The callbacks that should be executed on failure.
     *
     * @var array
     */
    public $failureCallbacks = [];

    /**
     * Indicate if the job should be deleted when models are missing.
     *
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * Create a new job instance.
     *
     * @param \CFunction_SerializableClosure $closure
     *
     * @return void
     */
    public function __construct(CFunction_SerializableClosure $closure) {
        $this->closure = $closure;
    }

    /**
     * Create a new job instance.
     *
     * @param \Closure $job
     *
     * @return self
     */
    public static function create($job) {
        return new self(new CFunction_SerializableClosure($job));
    }

    /**
     * Execute the job.
     *
     * @param \CContainer_Container $container
     *
     * @return void
     */
    public function handle(CContainer_Container $container) {
        $container->call($this->closure->getClosure(), ['job' => $this]);
    }

    /**
     * Add a callback to be executed if the job fails.
     *
     * @param callable $callback
     *
     * @return $this
     */
    public function onFailure($callback) {
        $this->failureCallbacks[] = $callback instanceof Closure
            ? new CQueue_SerializableClosure($callback)
            : $callback;

        return $this;
    }

    /**
     * Handle a job failure.
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function failed($e) {
        foreach ($this->failureCallbacks as $callback) {
            call_user_func($callback instanceof CQueue_SerializableClosure ? $callback->getClosure() : $callback, $e);
        }
    }

    /**
     * Get the display name for the queued job.
     *
     * @return string
     */
    public function displayName() {
        $reflection = new ReflectionFunction($this->closure->getClosure());

        return 'Closure (' . basename($reflection->getFileName()) . ':' . $reflection->getStartLine() . ')';
    }
}

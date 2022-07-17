<?php
class CBot_ExceptionHandler implements CBot_Contract_ExceptionHandlerInterface {
    protected $exceptions = [];

    public function __construct() {
        $this->exceptions = c::collect();
    }

    /**
     * Handle an exception.
     *
     * @param \Throwable $e
     * @param CBot_Bot   $bot
     *
     * @throws \Throwable
     *
     * @return mixed
     */
    public function handleException($e, CBot_Bot $bot) {
        $class = get_class($e);
        $handler = $this->exceptions->get($class);

        // Exact exception handler found, call it.
        if ($handler !== null) {
            call_user_func_array($handler, [$e, $bot]);

            return;
        }

        $parentExceptions = c::collect(class_parents($class));

        foreach ($parentExceptions as $exceptionClass) {
            if ($this->exceptions->has($exceptionClass)) {
                call_user_func_array($this->exceptions->get($exceptionClass), [$e, $bot]);

                return;
            }
        }

        // No matching parent exception, throw the exception away
        throw $e;
    }

    /**
     * Register a new exception type.
     *
     * @param string   $exception
     * @param callable $closure
     *
     * @return void
     */
    public function register($exception, callable $closure) {
        $this->exceptions->put($exception, $closure);
    }
}

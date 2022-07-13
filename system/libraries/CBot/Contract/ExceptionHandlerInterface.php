<?php

interface CBot_Contract_ExceptionHandlerInterface {
    /**
     * Handle an exception.
     *
     * @param \Throwable $e
     * @param CBot_Bot   $bot
     *
     * @return mixed
     */
    public function handleException($e, CBot_Bot $bot);

    /**
     * Register a new exception type.
     *
     * @param string   $exception
     * @param callable $closure
     *
     * @return mixed
     */
    public function register($exception, callable $closure);
}

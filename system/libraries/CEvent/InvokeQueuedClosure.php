<?php

class CEvent_InvokeQueuedClosure {
    /**
     * Handle the event.
     *
     * @param \CFunction_SerializableClosure $closure
     * @param array                          $arguments
     *
     * @return void
     */
    public function handle($closure, array $arguments) {
        call_user_func($closure->getClosure(), ...$arguments);
    }

    /**
     * Handle a job failure.
     *
     * @param \CFunction_SerializableClosure $closure
     * @param array                          $arguments
     * @param array                          $catchCallbacks
     * @param \Throwable                     $exception
     *
     * @return void
     */
    public function failed($closure, array $arguments, array $catchCallbacks, $exception) {
        $arguments[] = $exception;

        c::collect($catchCallbacks)->each->__invoke(...$arguments);
    }
}

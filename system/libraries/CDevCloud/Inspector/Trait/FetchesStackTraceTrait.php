<?php

trait CDevCloud_Inspector_Trait_FetchesStackTraceTrait {
    /**
     * Find the first frame in the stack trace outside of Telescope/Laravel.
     *
     * @return array
     */
    protected function getCallerFromStackTrace() {
        $trace = c::collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))->forget(0);

        return $trace->first(function ($frame) {
            if (!isset($frame['file'])) {
                return false;
            }

            return !cstr::contains($frame['file'], DOCROOT . 'system' . DS . 'libraries' . DS . 'vendor');
        });
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * A queued job that wants to know when its worker is interrupted.
 *
 * The worker calls `interrupted()` from the SIGQUIT/SIGTERM/SIGINT handler, so
 * the implementation runs inside a signal handler: keep it short, and use it to
 * set a flag the job's own loop checks rather than to do the cleanup itself.
 */
interface CQueue_InterruptibleInterface {
    /**
     * @param int $signal
     *
     * @return void
     */
    public function interrupted($signal);
}

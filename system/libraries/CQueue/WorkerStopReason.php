<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * Why a queue worker stopped.
 *
 * Laravel declares this as a backed enum; CF targets PHP 7.4, so the cases are
 * class constants carrying the same string values.
 */
class CQueue_WorkerStopReason {
    const LOST_CONNECTION = 'lostConnection';

    const INTERRUPTED = 'interrupted';

    const MAX_MEMORY_EXCEEDED = 'maxMemoryExceeded';

    const RECEIVED_RESTART_SIGNAL = 'receivedRestartSignal';

    const QUEUE_EMPTY = 'queueEmpty';

    const QUEUE_EMPTY_FOR = 'queueEmptyFor';

    const MAX_TIME_EXCEEDED = 'maxTimeExceeded';

    const MAX_JOBS_EXCEEDED = 'maxJobsExceeded';

    const TIMED_OUT = 'timedOut';
}

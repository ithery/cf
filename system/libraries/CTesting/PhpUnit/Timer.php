<?php

/**
 * Description of Timer
 *
 * @author Hery
 * @internal
 */
final class Timer {

    /**
     * @var float
     */
    private $start;

    /**
     * Timer constructor.
     */
    private function __construct($start) {
        $this->start = $start;
    }

    /**
     * Starts the timer.
     */
    public static function start() {
        return new self(microtime(true));
    }

    /**
     * Returns the elapsed time in microseconds.
     */
    public function result() {
        return microtime(true) - $this->start;
    }

}

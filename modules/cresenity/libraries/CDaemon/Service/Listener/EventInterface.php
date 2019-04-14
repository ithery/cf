<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 14, 2019, 10:45:14 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CDaemon_Service_Listener_EventInterface {

    /**
     * Read event.
     *
     * @var int
     */
    const EV_READ = 1;

    /**
     * Write event.
     *
     * @var int
     */
    const EV_WRITE = 2;

    /**
     * Except event
     *
     * @var int
     */
    const EV_EXCEPT = 3;

    /**
     * Signal event.
     *
     * @var int
     */
    const EV_SIGNAL = 4;

    /**
     * Timer event.
     *
     * @var int
     */
    const EV_TIMER = 8;

    /**
     * Timer once event.
     *
     * @var int
     */
    const EV_TIMER_ONCE = 16;

    /**
     * Add event listener to event loop.
     *
     * @param mixed    $fd
     * @param int      $flag
     * @param callable $func
     * @param mixed    $args
     * @return bool
     */
    public function add($fd, $flag, $func, $args = null);

    /**
     * Remove event listener from event loop.
     *
     * @param mixed $fd
     * @param int   $flag
     * @return bool
     */
    public function del($fd, $flag);

    /**
     * Remove all timers.
     *
     * @return void
     */
    public function clearAllTimer();

    /**
     * Main loop.
     *
     * @return void
     */
    public function loop();

    /**
     * Destroy loop.
     *
     * @return mixed
     */
    public function destroy();

    /**
     * Get Timer count.
     *
     * @return mixed
     */
    public function getTimerCount();
}

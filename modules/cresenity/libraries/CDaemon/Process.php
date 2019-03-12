<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 12, 2019, 5:38:20 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDaemon_Process {

    /**
     * The application will attempt to restart itself it encounters a recoverable fatal error after it's been running
     * for at least this many seconds. Prevents killing the server with process forking if the error occurs at startup.
     * @var integer
     */
    const MIN_RESTART_SECONDS = 10;

    /**
     * Events can be attached to each state using the on() method
     * @var integer
     */
    const ON_ERROR = 0;    // error() or fatal_error() is called
    const ON_SIGNAL = 1;    // the daemon has received a signal
    const ON_INIT = 2;    // the library has completed initialization, your setup() method is about to be called. Note: Not Available to Worker code.
    const ON_PREEXECUTE = 3;    // inside the event loop, right before your execute() method
    const ON_POSTEXECUTE = 4;    // and right after
    const ON_FORK = 5;    // in a background process right after it has been forked from the daemon
    const ON_PIDCHANGE = 6;    // whenever the pid changes -- in a background process for example
    const ON_IDLE = 7;    // called when there is idle time at the end of a loop_interval, or at the idle_probability when loop_interval isn't used
    const ON_REAP = 8;    // notification from the OS that a child process of this application has exited
    const ON_SHUTDOWN = 10;   // called at the top of the destructor

    /**
     * The frequency of the event loop. In seconds.
     *
     * In timer-based applications your execute() method will be called every $loop_interval seconds. Any remaining time
     * at the end of an event loop iteration will dispatch an ON_IDLE event and your application will sleep(). If the
     * event loop takes longer than the $loop_interval an error will be written to your application log.
     *
     * @example $this->loopInterval = 300;     // execute() will be called once every 5 minutes
     * @example $this->loopInterval = 0.5;     // execute() will be called 2 times every second
     * @example $this->loopInterval = 0;       // execute() will be called immediately -- There will be no sleep.
     *
     * @var float The interval in Seconds
     */

    protected $loopInterval = null;

    /**
     * Control how often the ON_IDLE event fires in applications that do not use a $loop_interval timer.
     *
     * The ON_IDLE event gives your application (and the PHP Simple Daemon library) a way to defer work to be run
     * when your application has idle time and would normally just sleep(). In timer-based applications that is very
     * deterministic. In applications that don't use the $loop_interval timer, this probability factor applied in each
     * iteration of the event loop to periodically dispatch ON_IDLE.
     *
     * Note: This value is completely ignored when using $loop_interval. In those cases, ON_IDLE is fired when there is
     *       remaining time at the end of your loop.
     *
     * Note: If you want to take responsibility for dispatching the ON_IDLE event in your application, just set
     *       this to 0 and dispatch the event periodically, eg:
     *       $this->dispatch(array(self::ON_IDLE));
     *
     * @var float The probability, from 0.0 to 1.0.
     */
    protected $idle_probability = 0.50;

    /**
     * The frequency of your application restarting itself. In seconds.
     *
     * @example $this->auto_restart_interval = 3600;    // Daemon will be restarted once an hour
     * @example $this->auto_restart_interval = 43200;   // Daemon will be restarted twice per day
     * @example $this->auto_restart_interval = 86400;   // Daemon will be restarted once per day
     *
     * @var integer The interval in Seconds
     */
    protected $auto_restart_interval = 43200;

    /**
     * Process ID
     * @var integer
     */
    private $pid;

    /**
     * Array of worker aliases
     * @var Array
     */
    private $workers = array();

    /**
     * Array of plugin aliases
     * @var Array
     */
    private $plugins = array();

    /**
     * Map of callbacks that have been registered using on()
     * @var Array
     */
    private $callbacks = array();

    /**
     * Runtime statistics for a recent window of execution
     * @var Array
     */
    private $stats = array();

    /**
     * Dictionary of application-wide environment vars with defaults.
     * @see Core_Daemon::set()
     * @see Core_Daemon::get()
     * @var array
     */
    private static $env = array(
        'parent' => true,
    );

    /**
     * Handle for log() method,
     * @see Core_Daemon::log()
     * @see Core_Daemon::restart();
     * @var stream
     */
    private static $log_handle = false;

}

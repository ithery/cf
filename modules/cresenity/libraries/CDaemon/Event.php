<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 17, 2019, 4:45:32 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDaemon_Event {

    use CEvent_Trait_Dispatchable;

    /**
     * Events can be attached to each state using the on() method
     * @var integer
     */
    const ON_ERROR = 0;    // error() or fatalError() is called
    const ON_SIGNAL = 1;    // the daemon has received a signal
    const ON_INIT = 2;    // the library has completed initialization, your setup() method is about to be called. Note: Not Available to Worker code.
    const ON_PREEXECUTE = 3;    // inside the event loop, right before your execute() method
    const ON_POSTEXECUTE = 4;    // and right after
    const ON_FORK = 5;    // in a background process right after it has been forked from the daemon
    const ON_PIDCHANGE = 6;    // whenever the pid changes -- in a background process for example
    const ON_IDLE = 7;    // called when there is idle time at the end of a loopInterval, or at the idleProbability when loopInterval isn't used
    const ON_REAP = 8;    // notification from the OS that a child process of this application has exited
    const ON_SHUTDOWN = 10;   // called at the top of the destructor

}

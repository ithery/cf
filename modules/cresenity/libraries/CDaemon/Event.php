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
    const ON_ERROR = 'CDaemon.OnError';    // error() or fatalError() is called
    const ON_SIGNAL = 'CDaemon.OnSignal';    // the daemon has received a signal
    const ON_INIT = 'CDaemon.OnInit';    // the library has completed initialization, your setup() method is about to be called. Note: Not Available to Worker code.
    const ON_PRE_EXECUTE = 'CDaemon.OnPreExecute';    // inside the event loop, right before your execute() method
    const ON_POST_EXECUTE = 'CDaemon.OnPostExecute';    // and right after
    const ON_FORK = 'CDaemon.OnFork';    // in a background process right after it has been forked from the daemon
    const ON_PID_CHANGE = 'CDaemon.OnPidChange';    // whenever the pid changes -- in a background process for example
    const ON_IDLE = 'CDaemon.OnIdle';    // called when there is idle time at the end of a loopInterval, or at the idleProbability when loopInterval isn't used
    const ON_REAP = 'CDaemon.OnReap';    // notification from the OS that a child process of this application has exited
    const ON_SHUTDOWN = 'CDaemon.OnShutdown';   // called at the top of the destructor

}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 17, 2019, 5:45:28 PM
 */
class CDaemon_Constant {
    const OS_TYPE_LINUX = 'linux';

    const OS_TYPE_WINDOWS = 'windows';

    /**
     * Events can be attached to each state using the on() method
     *
     * @var string
     */
    const EV_ERROR = 'CDaemon.OnError';    // error() or fatalError() is called

    const EV_SIGNAL = 'CDaemon.OnSignal';    // the daemon has received a signal

    const EV_INIT = 'CDaemon.OnInit';    // the library has completed initialization, your setup() method is about to be called. Note: Not Available to Worker code.

    const EV_PRE_EXECUTE = 'CDaemon.OnPreExecute';    // inside the event loop, right before your execute() method

    const EV_POST_EXECUTE = 'CDaemon.OnPostExecute';    // and right after

    const EV_FORK = 'CDaemon.OnFork';    // in a background process right after it has been forked from the daemon

    const EV_PID_CHANGE = 'CDaemon.OnPidChange';    // whenever the pid changes -- in a background process for example

    const EV_IDLE = 'CDaemon.OnIdle';    // called when there is idle time at the end of a loopInterval, or at the idleProbability when loopInterval isn't used

    const EV_REAP = 'CDaemon.OnReap';    // notification from the OS that a child process of this application has exited

    const EV_SHUTDOWN = 'CDaemon.OnShutdown';   // called at the top of the destructor
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

class CDaemon_Server_Constant {
    const EV_LISTENER_READ = 'OnRead';

    const EV_LISTENER_WRITE = 'OnWrite';

    const EV_READ = 1;

    const EV_WRITE = 2;

    const EV_EXCEPT = 3;

    const EV_SIGNAL = 4;

    const EV_TIMER = 8;

    const EV_TIMER_ONCE = 16;

    const SEND_FAIL = 2;

    const CONNECT_FAIL = 1;

    const VERSION = '1.0.0';
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 17, 2019, 4:52:31 PM
 */
class CDaemon_Event_Listener {
    /**
     * Read event.
     *
     * @var int
     */
    const ON_READ = 'CDaemon.Listener.OnRead';

    /**
     * Write event.
     *
     * @var int
     */
    const ON_WRITE = 'CDaemon.Listener.OnRead';
}

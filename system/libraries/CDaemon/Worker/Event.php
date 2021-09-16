<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Mar 17, 2019, 5:44:49 PM
 */
class CDaemon_Worker_Event extends CDaemon_Event {
    use CEvent_Trait_Dispatchable;
}

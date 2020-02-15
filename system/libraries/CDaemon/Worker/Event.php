<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 17, 2019, 5:44:49 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CDaemon_Worker_Event extends CDaemon_Event {

    use CEvent_Trait_Dispatchable;
}

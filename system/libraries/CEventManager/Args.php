<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 9:15:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * CEvent_Args is the base class for classes containing event data.
 *
 * This class contains no event data. It is used by events that do not pass state
 * information to an event handler when an event is raised. The single empty EventArgs
 * instance can be obtained through {@link getEmptyInstance}.
 */
class CEventManager_Args {

    /**
     * Single instance of EventArgs.
     *
     * @var EventArgs
     */
    private static $_emptyEventArgsInstance;

    /**
     * Gets the single, empty and immutable EventArgs instance.
     *
     * This instance will be used when events are dispatched without any parameter,
     * like this: EventManager::dispatchEvent('eventname');
     *
     * The benefit from this is that only one empty instance is instantiated and shared
     * (otherwise there would be instances for every dispatched in the abovementioned form).
     *
     * @see EventManager::dispatchEvent
     *
     * @link https://msdn.microsoft.com/en-us/library/system.eventargs.aspx
     *
     * @return CEventManager_Args
     */
    public static function getEmptyInstance() {
        if (!self::$_emptyEventArgsInstance) {
            self::$_emptyEventArgsInstance = new CEventManager_Args();
        }

        return self::$_emptyEventArgsInstance;
    }

}

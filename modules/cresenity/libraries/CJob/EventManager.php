<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 18, 2018, 11:23:47 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJob_EventManager {

    /**
     *
     * @var CEventManager
     */
    protected static $eventManager;
    protected static $callback = array();

    /**
     * 
     * @return CEventManager
     */
    public static function getEventManager() {
        if (self::$eventManager == null) {
            self::$eventManager = new CEventManager();
        }
        return self::$eventManager;
    }

    public static function addEventCallback($event, $callback) {
        if (self::getCallback($event) == null) {
            self::$callback[$event] = $callback;
        }
    }

    /**
     * Gets the listeners of a specific event or all listeners.
     *
     * @param string|null $event The name of the event.
     *
     * @return object[]|object[][] The event listeners for the specified event, or all event listeners.
     */
    public static function getCallback($event = null) {

        return $event ? (isset(self::$callback[$event]) ? self::$callback[$event] : null) : self::$callback;
    }

    public static function initialize() {
        $eventManager = self::getEventManager();
        if (!$eventManager->hasListeners(CJob_Events::onJobPreRun)) {
            $eventManager->addEventListener(CJob_Events::onJobPreRun, new CJob_EventManager_Listener());
        }
        if (!$eventManager->hasListeners(CJob_Events::onJobPostRun)) {
            $eventManager->addEventListener(CJob_Events::onJobPostRun, new CJob_EventManager_Listener());
        }
        if (!$eventManager->hasListeners(CJob_Events::onBackgroundJobPreRun)) {
            $eventManager->addEventListener(CJob_Events::onBackgroundJobPreRun, new CJob_EventManager_Listener());
        }
        if (!$eventManager->hasListeners(CJob_Events::onBackgroundJobPostRun)) {
            $eventManager->addEventListener(CJob_Events::onBackgroundJobPostRun, new CJob_EventManager_Listener());
        }
    }

}
